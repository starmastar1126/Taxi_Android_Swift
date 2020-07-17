//
//  ForgetPasswordUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 24/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ForgetPasswordUV: UIViewController, MyBtnClickDelegate, MyLabelClickDelegate, UITextFieldDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var lockImgView: UIImageView!
    @IBOutlet weak var forgetPasswordHLbl: MyLabel!
    @IBOutlet weak var forgetPassSubLbl: MyLabel!
    @IBOutlet weak var emailTxtField: MyTextField!
    @IBOutlet weak var forgetPasswordBtn: MyButton!
    @IBOutlet weak var backLbl: MyLabel!
    
    let generalFunc = GeneralFunctions()
    
    override func viewWillAppear(_ animated: Bool) {
//        self.navigationController?.navigationBar.isHidden = true
        self.configureRTLView()
    }
    
    
    override func viewWillDisappear(_ animated: Bool) {
//        self.navigationController?.navigationBar.isHidden = false
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.contentView.addSubview(self.generalFunc.loadView(nibName: "ForgetPasswordScreenDesign", uv: self, contentView: contentView))
        
        GeneralFunctions.setImgTintColor(imgView: lockImgView, color: UIColor.UCAColor.AppThemeColor)
        
        self.addBackBarBtn()
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FORGET_PASS_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FORGET_PASS_TXT")
        
        self.forgetPasswordHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FORGET_PASS_TXT")
        self.forgetPasswordHLbl.isHidden = true
        self.backLbl.isHidden = true
        
        self.emailTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_LBL_TXT"))
        self.forgetPassSubLbl.text = self.generalFunc.getLanguageLabel(origValue: "We just need your registered Email Id to sent you password reset instruction.", key: "LBL_FORGET_PASS_NOTE")
        self.forgetPassSubLbl.fitText()
        self.emailTxtField.getTextField()!.keyboardType = .emailAddress
        
        forgetPasswordBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_SUBMIT_TXT"))
        backLbl.text = "<< \(self.generalFunc.getLanguageLabel(origValue: "Back", key: "LBL_BACK"))"
        backLbl.setPadding(paddingTop: 10, paddingBottom: 10, paddingLeft: 10, paddingRight: 10)
        
        forgetPasswordBtn.clickDelegate = self
        self.backLbl.setClickDelegate(clickDelegate: self)
    }
    
    
    override func viewDidAppear(_ animated: Bool) {
        self.emailTxtField.getTextField()!.delegate = self
    }
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.backLbl){
            self.closeCurrentScreen()
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.forgetPasswordBtn){
            checkData()
        }
    }
    
    func checkData(){
        
        self.view.endEditing(true)
        
        let required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
    
        let emailEntered = Utils.checkText(textField: self.emailTxtField.getTextField()!) ? (GeneralFunctions.isValidEmail(testStr: Utils.getText(textField: self.emailTxtField.getTextField()!)) ? true : Utils.setErrorFields(textField: self.emailTxtField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_EMAIL_ERROR_TXT"))) : Utils.setErrorFields(textField: self.emailTxtField.getTextField()!, error: required_str)
        
        if(emailEntered){
            requestResetPassword()
        }
    }
    
    
    func requestResetPassword(){
    
        let parameters = ["type":"requestResetPassword","vEmail": Utils.getText(textField: self.emailTxtField.getTextField()!), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.emailTxtField.setText(text: "")
                    
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        self.closeCurrentScreen()
                    })
                    
                    return
                }
                
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))

                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    //MARK:- TextfieldDelegate Method
    func textFieldDidEndEditing(_ textField: UITextField) {
        UIApplication.shared.isStatusBarHidden = true
        UIApplication.shared.isStatusBarHidden = false
    }

}
