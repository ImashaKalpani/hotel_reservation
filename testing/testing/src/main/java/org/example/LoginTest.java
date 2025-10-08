package org.example;

import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.io.FileHandler;
import org.testng.Assert;
import org.testng.ITestResult;
import org.testng.annotations.*;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;

public class LoginTest {
    WebDriver driver;

    @BeforeClass
    public void setup() {
        System.setProperty("webdriver.chrome.driver", "D:\\Browser drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.manage().window().maximize();
        driver.get("https://luxestayslk.lovestoblog.com/login.php");
    }

    @Test
    public void testValidLogin() throws InterruptedException {
        // Wait before entering email
        Thread.sleep(1000);
        driver.findElement(By.name("email")).sendKeys("user1@test.com");

        // Wait before entering password
        Thread.sleep(1000);
        driver.findElement(By.name("password")).sendKeys("Test@123");

        // Wait before clicking login
        Thread.sleep(1000);
        driver.findElement(By.className("btn")).click();

        // Wait for page load
        Thread.sleep(7000);

        Assert.assertTrue(driver.getCurrentUrl().contains("index.php"), "Login failed!");
    }

    @AfterMethod
    public void captureScreenshot(ITestResult result) throws IOException {
        if (ITestResult.FAILURE == result.getStatus() || ITestResult.SUCCESS == result.getStatus()) {
            TakesScreenshot ts = (TakesScreenshot) driver;
            File src = ts.getScreenshotAs(OutputType.FILE);

            // Save with timestamp and test name
            String timestamp = new SimpleDateFormat("yyyyMMdd_HHmmss").format(new Date());
            File dest = new File("D:\\Screenshots\\" + result.getName() + "_" + timestamp + ".png");

            FileHandler.copy(src, dest);
            System.out.println("Screenshot saved at: " + dest.getAbsolutePath());
        }
    }

    @AfterClass
    public void tearDown() {
        driver.quit();
    }
}
