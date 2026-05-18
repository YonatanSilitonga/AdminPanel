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

WebUI.click(findTestObject('Page_Toba Tourism - Admin Panel/input_admintobatourism.id'))

WebUI.rightClick(findTestObject('Page_Toba Tourism - Admin Panel/input_admintobatourism.id'))

WebUI.setText(findTestObject('Page_Toba Tourism - Admin Panel/input_admintobatourism.id'), 'superadmin@smarttourism.local')

WebUI.rightClick(findTestObject('Page_Toba Tourism - Admin Panel/input_'))

WebUI.setEncryptedText(findTestObject('Page_Toba Tourism - Admin Panel/input_'), 'QWNwcgrD6Z3ZW8ArihRsCA==')

WebUI.click(findTestObject('Page_Toba Tourism - Admin Panel/button_Masuk'))

WebUI.click(findTestObject('Page_Dashboard Overview - Smart Tourism/a_Fasilitas Umum'))

WebUI.click(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/button_Tambah Fasilitas'))

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/input_Contoh_ SPBU Balige Utara'), 'ATM Mandiri')

WebUI.selectOptionByValue(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/select_type'), 'ATM', false)

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/textarea_Contoh_ Jl. Sisingamangaraja No. 12'), 
    'Jl. Balige')

WebUI.click(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/div_div'))

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/input_Contoh_ 06.00-22.00'), '06.00-22.00')

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/input_Contoh_ 62 812 3456 7890'), '+62 812 6754 8965')

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/textarea_Ceritakan tentang fasilitas ini'), 'ATM yang lengkap')

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/input_Pisahkan dengan koma. Contoh_ Free Wi-Fi,'), 
    'Area Parkir')

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/input_Pisahkan dengan koma. Contoh_ Pemandangan'), 
    'Daerah Kota')

WebUI.click(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/div_Upload foto (Opsional)'))

WebUI.setText(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/input_Upload foto (Opsional)'), 'C:\\Users\\user\\Pictures\\Screenshots\\Screenshot 2026-02-17 202131.png')

WebUI.click(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/button_Simpan Fasilitas'))

WebUI.verifyElementPresent(findTestObject('Page_Kelola Fasilitas Umum - Smart Tourism/img_ATM Mandiri'), 0)

