package org.example;

import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.io.FileHandler;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.ITestResult;
import org.testng.annotations.*;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.time.Duration;
import java.util.Date;
import java.util.List;

public class CancelBookingTest {
    WebDriver driver;
    WebDriverWait wait;

    @BeforeClass
    public void setup() {
        System.setProperty("webdriver.chrome.driver", "D:\\Browser drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.manage().window().maximize();
        wait = new WebDriverWait(driver, Duration.ofSeconds(30)); // increased to 30s
    }

    @Test
    public void cancelBooking() throws InterruptedException {
        // Navigate to login page
        driver.get("https://luxestayslk.lovestoblog.com/login.php");
        Thread.sleep(2000);

        // Login
        WebElement emailInput = wait.until(ExpectedConditions.visibilityOfElementLocated(By.name("email")));
        emailInput.sendKeys("user2@test.com");
        Thread.sleep(1000);

        WebElement passwordInput = driver.findElement(By.name("password"));
        passwordInput.sendKeys("Test@1234");
        Thread.sleep(1000);

        WebElement loginButton = driver.findElement(By.className("btn"));
        loginButton.click();

        // Wait until dashboard loads
        wait.until(ExpectedConditions.urlContains("index.php"));
        Thread.sleep(2000);

        // Navigate to bookings page
        driver.get("https://luxestayslk.lovestoblog.com/bookings.php");
        Thread.sleep(2000);

        // Wait until booking cards are visible
        List<WebElement> bookingCards = wait.until(
                ExpectedConditions.visibilityOfAllElementsLocatedBy(By.className("booking-card"))
        );

        if (!bookingCards.isEmpty()) {
            WebElement firstBooking = bookingCards.get(0);

            // Find the cancel link inside this booking card
            WebElement cancelLink;
            try {
                cancelLink = firstBooking.findElement(By.xpath(".//a[contains(text(),'Cancel Booking')]"));
            } catch (NoSuchElementException e) {
                Assert.fail("Cancel Booking link not found inside first booking card");
                return;
            }

            // Wait until preloader is invisible
            try {
                wait.until(ExpectedConditions.invisibilityOfElementLocated(By.id("preloader")));
            } catch (TimeoutException te) {
                System.out.println("Preloader still present or not found; proceeding carefully");
            }

            Thread.sleep(1000);

            // Wait until cancel link is clickable
            wait.until(ExpectedConditions.elementToBeClickable(cancelLink));
            Thread.sleep(1000);

            // Click using JavaScript to bypass overlays
            ((JavascriptExecutor) driver).executeScript("arguments[0].click();", cancelLink);

            // Handle confirm alert
            wait.until(ExpectedConditions.alertIsPresent());
            Thread.sleep(1000);
            driver.switchTo().alert().accept();

            // Wait until the first booking card disappears (staleness)
            wait.until(ExpectedConditions.stalenessOf(firstBooking));
            Thread.sleep(2000);

            System.out.println("✅ Booking cancelled successfully!");
        } else {
            System.out.println("⚠️ No bookings found to cancel.");
        }
    }

    @AfterMethod
    public void captureScreenshotOnFailure(ITestResult result) throws IOException {
        if (ITestResult.FAILURE == result.getStatus()) {
            TakesScreenshot ts = (TakesScreenshot) driver;
            File src = ts.getScreenshotAs(OutputType.FILE);

            String folderPath = "D:\\Screenshots";
            File folder = new File(folderPath);
            if (!folder.exists()) {
                folder.mkdirs();
            }

            String timestamp = new SimpleDateFormat("yyyyMMdd_HHmmss").format(new Date());
            File dest = new File(folderPath + "\\" + result.getName() + "_" + timestamp + ".png");

            FileHandler.copy(src, dest);
            System.out.println("📸 Screenshot captured for failed test: " + dest.getAbsolutePath());
        }
    }

    @AfterClass
    public void tearDown() {
        if (driver != null) {
            driver.quit();
        }
    }
}
