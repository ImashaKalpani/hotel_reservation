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

public class HotelSearchTest {
    WebDriver driver;

    @BeforeClass
    public void setup() {
        System.setProperty("webdriver.chrome.driver", "D:\\Browser drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.manage().window().maximize();
        driver.get("https://luxestayslk.lovestoblog.com/index.php");
    }

    @Test
    public void testHotelSearch() throws InterruptedException {
        // Wait for page to load
        Thread.sleep(2000);

        // Enter search query
        driver.findElement(By.name("q")).sendKeys("Colombo");

        // Wait before clicking search
        Thread.sleep(1000);

        // Click search button
        driver.findElement(By.className("btn")).click();

        // Wait for results to load
        Thread.sleep(7000);

        // Verify results
        Assert.assertTrue(driver.getPageSource().contains("#hotels"), "Hotel search failed!");
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
