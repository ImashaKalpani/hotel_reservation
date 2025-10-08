package org.example;

import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.io.FileHandler;
import org.openqa.selenium.support.ui.*;
import org.testng.ITestResult;
import org.testng.annotations.*;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.time.Duration;
import java.util.Date;
import java.util.List;

public class BookingTest {
    WebDriver driver;
    WebDriverWait wait;

    @BeforeClass
    public void setup() {
        System.setProperty("webdriver.chrome.driver", "D:\\Browser drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.manage().window().maximize();
        wait = new WebDriverWait(driver, Duration.ofSeconds(20));
    }

    @Test
    public void testLoginAndBooking() throws InterruptedException {
        // Navigate to login page
        driver.get("https://luxestayslk.lovestoblog.com/login.php");

        // Login
        WebElement emailInput = wait.until(ExpectedConditions.visibilityOfElementLocated(By.name("email")));
        emailInput.sendKeys("user2@test.com");

        WebElement passwordInput = driver.findElement(By.name("password"));
        passwordInput.sendKeys("Test@1234");

        WebElement loginButton = driver.findElement(By.className("btn"));
        loginButton.click();

        Thread.sleep(2000);

        // Wait until hotels section is visible after login
        WebElement hotelsSection = wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("hotels")));

        // Wait for preloader to disappear if exists
        wait.until(ExpectedConditions.invisibilityOfElementLocated(By.id("preloader")));

        Thread.sleep(2000);

        // Click first hotel's "View Details"
        List<WebElement> hotels = hotelsSection.findElements(By.cssSelector(".hotel"));
        WebElement viewButton = hotels.get(0).findElement(By.cssSelector(".hotel-footer .btn"));
        viewButton.click();

        Thread.sleep(2000);

        // Wait for room dropdown to be visible
        WebElement roomDropdown = wait.until(ExpectedConditions.visibilityOfElementLocated(By.name("room_id")));
        Select select = new Select(roomDropdown);
        select.selectByValue("34"); // Family Room

        // Fill check-in/out dates
        WebElement checkIn = wait.until(ExpectedConditions.visibilityOfElementLocated(By.name("checkin")));
        checkIn.clear();
        checkIn.sendKeys("11/25/2025");

        WebElement checkOut = driver.findElement(By.name("checkout"));
        checkOut.clear();
        checkOut.sendKeys("11/26/2025");

        Thread.sleep(2000);

        // Click confirm/booking button
        WebElement confirmButton = driver.findElement(By.className("btn"));
        confirmButton.click();

        Thread.sleep(5000);
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
            System.out.println("Screenshot captured for failed test: " + dest.getAbsolutePath());
        }
    }

    @AfterClass
    public void tearDown() {
        if (driver != null) {
            driver.quit();
        }
    }
}
