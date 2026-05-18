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

WebUI.click(findTestObject('Page_Kelola Destinasi - Smart Tourism/button_Tambah Destinasi'))

WebUI.setText(findTestObject('Page_Kelola Destinasi - Smart Tourism/input_Festival Danau Toba'), 'Pantai pasir putih')

WebUI.selectOptionByValue(findTestObject('Page_Kelola Destinasi - Smart Tourism/select_category'), 'beach', false)

WebUI.setText(findTestObject('Page_Kelola Destinasi - Smart Tourism/input_Balige, Toba'), 'Balige, toba')

WebUI.setText(findTestObject('Page_Kelola Destinasi - Smart Tourism/input_flex-1 min-w-0 border border-gray-200 round_1'), 
    '09:00')

WebUI.setText(findTestObject('Page_Kelola Destinasi - Smart Tourism/input_flex-1 min-w-0 border border-gray-200 round_1'), 
    '18:00')

WebUI.click(findTestObject('Page_Kelola Destinasi - Smart Tourism/div_Nama Destinasi'))

WebUI.setText(findTestObject('Page_Kelola Destinasi - Smart Tourism/textarea_Deskripsi singkat destinasi'), 'Pantai yang sangat indah')

WebUI.click(findTestObject('Page_Kelola Destinasi - Smart Tourism/div_Klik untuk upload foto'))

WebUI.setText(findTestObject('Page_Kelola Destinasi - Smart Tourism/input_Klik untuk upload foto'), 'D:\\LUMBAN BULBUL.jpg')

WebUI.click(findTestObject('Page_Kelola Destinasi - Smart Tourism/button_Simpan Destinasi'))

WebUI.verifyElementVisible(findTestObject('Page_Kelola Destinasi - Smart Tourism/div_Validation Errors'))

