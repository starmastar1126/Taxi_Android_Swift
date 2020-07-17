//
//  BankDetailsUV.swift
//  DriverApp
//
//  Created by Admin on 20/09/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class BankDetailsUV: UIViewController,MyBtnClickDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    
    //Bank Details OutLets
    @IBOutlet weak var paymentEmailField: MyTextField!
    @IBOutlet weak var bankAccountHolderNameField: MyTextField!
    @IBOutlet weak var accountNumberField: MyTextField!
    @IBOutlet weak var bankNameField: MyTextField!
    @IBOutlet weak var bankLocationField: MyTextField!
    @IBOutlet weak var BIC_SWIFTCodeField: MyTextField!
    @IBOutlet weak var submitBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    
    var cntView:UIView!
    var PAGE_HEIGHT:CGFloat = 600
    var isFirstLaunch = true
    var loaderView:UIView!
    var required_str = ""
    //    typealias Text = getTextField() - error
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.addBackBarBtn()
        
        cntView = self.generalFunc.loadView(nibName: "BankDetailsScreenDesign", uv: self, contentView: scrollView)
        self.scrollView.addSubview(cntView)
        self.scrollView.isHidden = true
        
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
        
        self.scrollView.bounces = false
        
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
        
        setData()
    }
    
    func setData(){
        required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BANK_DETAILS_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BANK_DETAILS_TXT")
        self.paymentEmailField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PAYMENT_EMAIL_TXT"))
        self.bankAccountHolderNameField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROFILE_BANK_HOLDER_TXT"))
        self.accountNumberField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_NUMBER"))
        self.bankNameField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BANK_NAME"))
        self.bankLocationField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BANK_LOCATION"))
        self.BIC_SWIFTCodeField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BIC_SWIFT_CODE"))
        self.submitBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Submit", key: "LBL_SUBMIT_BUTTON_TXT"))
        self.submitBtn.clickDelegate = self
        loadDriverBankDetails(eDisplay: "Yes")
    }
    
    func myBtnTapped(sender:MyButton){
        
        if (sender == self.submitBtn){
            submitBankDetails()
        }
    }
    
    func submitBankDetails(){
        let paymentEmailEntered = Utils.checkText(textField: self.paymentEmailField.getTextField()!) ? (GeneralFunctions.isValidEmail(testStr: Utils.getText(textField: self.paymentEmailField.getTextField()!)) ? true : Utils.setErrorFields(textField: self.paymentEmailField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_EMAIL_ERROR_TXT"))) : Utils.setErrorFields(textField: self.paymentEmailField.getTextField()!, error: required_str)
        
        let bankAccountHolderNameEntered = Utils.checkText(textField: self.bankAccountHolderNameField.getTextField()!) ? true : Utils.setErrorFields(textField: self.bankAccountHolderNameField.getTextField()!, error: required_str)
        
        let accountNumberEntered = Utils.checkText(textField: self.accountNumberField.getTextField()!) ? true : Utils.setErrorFields(textField: self.accountNumberField.getTextField()!, error: required_str)
        
        let bankNameEntered = Utils.checkText(textField: self.bankNameField.getTextField()!) ? true : Utils.setErrorFields(textField: self.bankNameField.getTextField()!, error: required_str)
        
        let bankLocationEntered = Utils.checkText(textField: self.bankLocationField.getTextField()!) ? true : Utils.setErrorFields(textField: self.bankLocationField.getTextField()!, error: required_str)
        
        let BIC_SWIFTCodeEntered = Utils.checkText(textField: self.BIC_SWIFTCodeField.getTextField()!) ? true : Utils.setErrorFields(textField: self.BIC_SWIFTCodeField.getTextField()!, error: required_str)
        
        if (paymentEmailEntered == false || bankAccountHolderNameEntered == false || accountNumberEntered == false || bankNameEntered == false || bankLocationEntered == false || BIC_SWIFTCodeEntered == false) {
            return;
        }
        
        loadDriverBankDetails(eDisplay: "No")
    }
    
    func loadDriverBankDetails(eDisplay:String){
        if(self.loaderView != nil){
            self.loaderView.removeFromSuperview()
        }
        loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
        loaderView.backgroundColor = UIColor.clear
        
        let parameters = ["type":"DriverBankDetails","iDriverId": GeneralFunctions.getMemberd(), "UserType":Utils.appUserType,"eDisplay":"\(eDisplay)","vPaymentEmail":Utils.getText(textField: self.paymentEmailField.getTextField()!),"vBankAccountHolderName":Utils.getText(textField: self.bankAccountHolderNameField.getTextField()!),"vAccountNumber":Utils.getText(textField: self.accountNumberField.getTextField()!),"vBankLocation":Utils.getText(textField: self.bankLocationField.getTextField()!),"vBankName":Utils.getText(textField: self.bankNameField.getTextField()!),"vBIC_SWIFT_Code":Utils.getText(textField: self.BIC_SWIFTCodeField.getTextField()!)]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    let msgData = dataDict.getObj(Utils.message_str)
                    
                    
                    if(eDisplay == "Yes"){
                        
                        self.scrollView.isHidden = false
                        
                        self.paymentEmailField.setText(text:msgData.get("vPaymentEmail"))
                        self.bankAccountHolderNameField.setText(text: msgData.get("vBankAccountHolderName"))
                        self.accountNumberField.setText(text: msgData.get("vAccountNumber"))
                        self.bankLocationField.setText(text: msgData.get("vBankLocation"))
                        self.bankNameField.setText(text: msgData.get("vBankName"))
                        self.BIC_SWIFTCodeField.setText(text: msgData.get("vBIC_SWIFT_Code"))
                        
                    }else if(eDisplay == "No"){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BANK_DETAILS_UPDATED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                            
                            self.closeCurrentScreen()
                        })
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            self.loaderView.isHidden = true
        })
    }
    
}

