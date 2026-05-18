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

WebUI.navigateToUrl('http://127.0.0.1:8000/')

WebUI.click(findTestObject('Sistem Login AND Middleware Auth/Page_Toba Tourism - Admin Panel/input_admintobatourism.id'))

WebUI.rightClick(findTestObject('Sistem Login AND Middleware Auth/Page_Toba Tourism - Admin Panel/input_admintobatourism.id'))

WebUI.setText(findTestObject('Sistem Login AND Middleware Auth/Page_Toba Tourism - Admin Panel/input_admintobatourism.id'), 
    'superadmin@smarttourism.local')

WebUI.setEncryptedText(findTestObject('Sistem Login AND Middleware Auth/Page_Toba Tourism - Admin Panel/input_'), 'QWNwcgrD6Z3ZW8ArihRsCA==')

WebUI.click(findTestObject('Sistem Login AND Middleware Auth/Page_Toba Tourism - Admin Panel/button_Masuk'))

WebUI.click(findTestObject('Sistem Login AND Middleware Auth/Page_Dashboard Overview - Smart Tourism/button_Destinasi'))

WebUI.click(findTestObject('Sistem Login AND Middleware Auth/Page_Dashboard Overview - Smart Tourism/a_Kelola Destinasi'))

WebUI.click(findTestObject('Sistem Login AND Middleware Auth/Page_Kelola Destinasi - Smart Tourism/button_p-2.5 bg-sidebar-active_5 text-sidebar-ac'))

WebUI.setText(findTestObject('Sistem Login AND Middleware Auth/Page_Kelola Destinasi - Smart Tourism/input_name'), 'Budaya indah saung alam raya')

WebUI.click(findTestObject('Sistem Login AND Middleware Auth/Page_Kelola Destinasi - Smart Tourism/button_Simpan Perubahan'))

