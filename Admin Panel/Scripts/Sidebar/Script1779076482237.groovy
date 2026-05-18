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

WebUI.click(findTestObject('Page_Dashboard Overview - Smart Tourism/button_Destinasi'))

WebUI.click(findTestObject('Page_Dashboard Overview - Smart Tourism/a_Kelola Destinasi'))

WebUI.click(findTestObject('Page_Kelola Destinasi - Smart Tourism/a_Trending Destinasi'))

WebUI.click(findTestObject('Page_Trending Destinasi - Smart Tourism/a_Kelola Event'))

WebUI.click(findTestObject('Page_Daftar Event - Smart Tourism/a_Carousel dan Banner'))

WebUI.click(findTestObject('Page_Carousel dan Banner - Smart Tourism/a_Fasilitas Umum'))

WebUI.click(findTestObject('null'))

WebUI.click(findTestObject('Page_Berita  Promosi - Smart Tourism/a_Budaya dan Warisan'))

WebUI.click(findTestObject('Page_Budaya dan Warisan - Smart Tourism/a_Manajemen Pengguna'))

WebUI.click(findTestObject('Page_Manajemen Pengguna - Smart Tourism/button_Ulasan  Laporan'))

WebUI.click(findTestObject('Page_Manajemen Pengguna - Smart Tourism/a_Ringkasan Ulasan'))

WebUI.click(findTestObject('Page_Ulasan Pengguna - Smart Tourism/a_Laporan Masuk'))

WebUI.click(findTestObject('Page_Laporan Pengguna - Smart Tourism/a_Pengaturan Sistem'))

WebUI.closeBrowser()

