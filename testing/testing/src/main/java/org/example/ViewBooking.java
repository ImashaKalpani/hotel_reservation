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

public class ViewBooking {
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
    public void viewMyBookings() throws InterruptedException {
        // Navigate to login page
        driver.get("https://luxestayslk.lovestoblog.com/login.php");

        // Login
        WebElement emailInput = wait.until(ExpectedConditions.visibilityOfElementLocated(By.name("email")));
        emailInput.sendKeys("user1@test.com");

        WebElement passwordInput = driver.findElement(By.name("password"));
        passwordInput.sendKeys("Test@123");

        WebElement loginButton = driver.findElement(By.className("btn"));
        loginButton.click();

        // Wait until login is successful and dashboard/hotels page loads
        wait.until(ExpectedConditions.urlContains("index.php"));

        Thread.sleep(2000);

        // Navigate to "My Bookings" page
        driver.get("https://luxestayslk.lovestoblog.com/bookings.php");

        Thread.sleep(2000);

        // Wait until bookings table or section is visible
        WebElement bookingsTable = wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("container")));

        System.out.println("Navigated to My Bookings page successfully!");
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
