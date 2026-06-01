import static com.kms.katalon.core.checkpoint.CheckpointFactory.findCheckpoint
import static com.kms.katalon.core.testcase.TestCaseFactory.findTestCase
import static com.kms.katalon.core.testdata.TestDataFactory.findTestData
import static com.kms.katalon.core.testobject.ObjectRepository.findTestObject
import static com.kms.katalon.core.testobject.ObjectRepository.findWindowsObject
import com.kms.katalon.core.checkpoint.Checkpoint as Checkpoint
import com.kms.katalon.core.cucumber.keyword.CucumberBuiltinKeywords as CucumberKW
import com.kms.katalon.core.mobile.keyword.MobileBuiltInKeywords as Mobile
import com.kms.katalon.core.model.FailureHandling as FailureHandling
import com.kms.katalon.core.testcase.TestCase as TestCase
import com.kms.katalon.core.testdata.TestData as TestData
import com.kms.katalon.core.testng.keyword.TestNGBuiltinKeywords as TestNGKW
import com.kms.katalon.core.testobject.TestObject as TestObject
import com.kms.katalon.core.webservice.keyword.WSBuiltInKeywords as WS
import com.kms.katalon.core.webui.keyword.WebUiBuiltInKeywords as WebUI
import com.kms.katalon.core.windows.keyword.WindowsBuiltinKeywords as Windows
import internal.GlobalVariable as GlobalVariable
import org.openqa.selenium.Keys as Keys

WebUI.openBrowser('')

WebUI.navigateToUrl('http://127.0.0.1:8000/admin/login')

WebUI.setText(findTestObject('Page_Toba Tourism - Admin Panel/input_admintobatourism.id'), 'superadmin@smarttourism.local')

WebUI.setEncryptedText(findTestObject('Page_Toba Tourism - Admin Panel/input_'), 'QWNwcgrD6Z3ZW8ArihRsCA==')

WebUI.click(findTestObject('Page_Toba Tourism - Admin Panel/button_Masuk'))

WebUI.click(findTestObject('Page_Dashboard Overview - Smart Tourism/button_Ulasan  Laporan'))

WebUI.click(findTestObject('Page_Dashboard Overview - Smart Tourism/a_Laporan Masuk'))

WebUI.verifyElementPresent(findTestObject('Page_Laporan Pengguna - Smart Tourism/input_Cari deskripsi laporan'), 0)

WebUI.verifyElementVisible(findTestObject('Page_Laporan Pengguna - Smart Tourism/div_Menampilkan 1 - 9 dari 9 data'))

WebUI.setText(findTestObject('Page_Laporan Pengguna - Smart Tourism/input_Cari deskripsi laporan'), 'informasi')

WebUI.sendKeys(findTestObject('Page_Laporan Pengguna - Smart Tourism/input_Cari deskripsi laporan'), Keys.chord(Keys.ENTER))

WebUI.verifyElementVisible(findTestObject('Page_Laporan Pengguna - Smart Tourism/div_Menampilkan 1 - 2 dari 2 data'))

WebUI.closeBrowser()

