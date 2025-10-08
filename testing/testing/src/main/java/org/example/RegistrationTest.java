package org.example;

import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.io.FileHandler;
import org.testng.ITestResult;
import org.testng.annotations.*;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;

public class RegistrationTest {
    WebDriver driver;

    @BeforeClass
    public void setup() throws InterruptedException {
        System.setProperty("webdriver.chrome.driver", "D:\\Browser drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.manage().window().maximize();
        driver.get("https://luxestayslk.lovestoblog.com/register.php");
        Thread.sleep(2000); // wait 2 seconds for page to load
    }

    @Test
    public void testUserRegistration() throws InterruptedException {
        driver.findElement(By.name("name")).sendKeys("Tester2");
        Thread.sleep(500);

        driver.findElement(By.name("email")).sendKeys("user2@test.com");
        Thread.sleep(500);

        driver.findElement(By.name("password")).sendKeys("Test@1234");
        Thread.sleep(500);

        driver.findElement(By.name("confirm")).sendKeys("Test@1234"); // confirm password
        Thread.sleep(500);

        driver.findElement(By.className("btn")).click();
        Thread.sleep(2000); // wait for success message
    }

    @AfterMethod
    public void captureScreenshot(ITestResult result) throws IOException {
        if (ITestResult.FAILURE == result.getStatus() || ITestResult.SUCCESS == result.getStatus()) {
            TakesScreenshot ts = (TakesScreenshot) driver;
            File src = ts.getScreenshotAs(OutputType.FILE);

            // Folder path
            String folderPath = "D:\\Screenshots";
            File folder = new File(folderPath);

            // Create folder if it doesn’t exist
            if (!folder.exists()) {
                folder.mkdirs();
            }

            // Save with timestamp and test name
            String timestamp = new SimpleDateFormat("yyyyMMdd_HHmmss").format(new Date());
            File dest = new File(folderPath + "\\" + result.getName() + "_" + timestamp + ".png");

            FileHandler.copy(src, dest);
            System.out.println("Screenshot saved at: " + dest.getAbsolutePath());
        }
    }

    @AfterClass
    public void tearDown() {
        driver.quit();
    }
}
