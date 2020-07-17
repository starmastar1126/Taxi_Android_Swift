//
//  OpenChangePassword.swift
//  DriverApp
//
//  Created by NEW MAC on 13/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenChangePassword: NSObject {
    
    typealias CompletionHandler = (_ isPasswordChanged:Bool) -> Void
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var vPassword = ""
    
    let generalFunc = GeneralFunctions()
    var changePasswordView:ChangePasswordView!
    var bgView:UIView!
    var handler:CompletionHandler!
    var userProfileJson:NSDictionary!
    var SITE_TYPE_DEMO_MSG = ""
    var loadingDialog:NBMaterialLoadingDialog!
    
    init(uv:UIViewController, containerView:UIView, vPassword:String, userProfileJson: NSDictionary, SITE_TYPE_DEMO_MSG:String){
        self.uv = uv
        self.containerView = containerView
        self.vPassword = vPassword
        self.userProfileJson = userProfileJson
        self.SITE_TYPE_DEMO_MSG = SITE_TYPE_DEMO_MSG
        super.init()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(){
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        let bgTapGue = UITapGestureRecognizer()
        bgTapGue.addTarget(self, action: #selector(self.removeView))
        bgView.addGestureRecognizer(bgTapGue)
        
        
        changePasswordView = ChangePasswordView(frame: CGRect(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2, width: (Application.screenSize.width > 390 ? 375 : (Application.screenSize.width - 50)), height: 390))
        
        changePasswordView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        changePasswordView.setViewHandler { (view, isPositiveBtnClicked) in
            
            if(isPositiveBtnClicked == true){
                
                let required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
                let noWhiteSpace = self.generalFunc.getLanguageLabel(origValue: "Password should not contain whitespace.", key: "LBL_ERROR_NO_SPACE_IN_PASS")
                let pass_length = self.generalFunc.getLanguageLabel(origValue: "Password must be", key: "LBL_ERROR_PASS_LENGTH_PREFIX")
                    + " \(Utils.minPasswordLength)"  + self.generalFunc.getLanguageLabel(origValue: "or more character long.",key: "LBL_ERROR_PASS_LENGTH_SUFFIX")
                
                let currPasswordEntered = self.vPassword != "" ? (Utils.checkText(textField: self.changePasswordView.currentPassTxtField.getTextField()!) ? (Utils.getText(textField: self.changePasswordView.currentPassTxtField.getTextField()!).contains(" ") ? Utils.setErrorFields(textField: self.changePasswordView.currentPassTxtField.getTextField()!, error: noWhiteSpace) : (Utils.getText(textField: self.changePasswordView.currentPassTxtField.getTextField()!).characters.count >= Utils.minPasswordLength ? true : Utils.setErrorFields(textField: self.changePasswordView.currentPassTxtField.getTextField()!, error: pass_length))) : Utils.setErrorFields(textField: self.changePasswordView.currentPassTxtField.getTextField()!, error: required_str)) : true
                
                let newPasswordEntered = Utils.checkText(textField: self.changePasswordView.newPassTxtField.getTextField()!) ? (Utils.getText(textField: self.changePasswordView.newPassTxtField.getTextField()!).contains(" ") ? Utils.setErrorFields(textField: self.changePasswordView.newPassTxtField.getTextField()!, error: noWhiteSpace) : (Utils.getText(textField: self.changePasswordView.newPassTxtField.getTextField()!).characters.count >= Utils.minPasswordLength ? true : Utils.setErrorFields(textField: self.changePasswordView.newPassTxtField.getTextField()!, error: pass_length))) : Utils.setErrorFields(textField: self.changePasswordView.newPassTxtField.getTextField()!, error: required_str)
                
                let reEnterNewPasswordEntered = Utils.checkText(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!) ? (Utils.getText(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!).contains(" ") ? Utils.setErrorFields(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!, error: noWhiteSpace) : (Utils.getText(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!).characters.count >= Utils.minPasswordLength ? (Utils.getText(textField: self.changePasswordView.newPassTxtField.getTextField()!) != Utils.getText(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!) ? Utils.setErrorFields(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_VERIFY_PASSWORD_ERROR_TXT")) : true) : Utils.setErrorFields(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!, error: pass_length))) : Utils.setErrorFields(textField: self.changePasswordView.reEnterNewPassTxtField.getTextField()!, error: required_str)
                
                if(currPasswordEntered == true && newPasswordEntered == true && reEnterNewPasswordEntered == true){
                    
//                    self.removeView()
                    
                    self.changePassword(currentPassword: Utils.getText(textField: self.changePasswordView.currentPassTxtField.getTextField()!), password: Utils.getText(textField: self.changePasswordView.newPassTxtField.getTextField()!))
                }
                
            }else{
                
                self.removeView()
            }
        }
        
        Utils.createRoundedView(view: changePasswordView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        changePasswordView.layer.shadowOpacity = 0.5
        changePasswordView.layer.shadowOffset = CGSize(width: 0, height: 3)
        changePasswordView.layer.shadowColor = UIColor.black.cgColor
        
        
//        let currentWindow = Application.window
        
//        if(currentWindow != nil){
//            currentWindow?.addSubview(bgView)
//            currentWindow?.addSubview(changePasswordView)
//        }else{
//            self.uv.view.addSubview(bgView)
//            self.uv.view.addSubview(changePasswordView)
//        }
        
        let currentWindow = Application.window
        
        if(self.uv == nil){
            currentWindow?.addSubview(bgView)
            currentWindow?.addSubview(changePasswordView)
        }else if(self.uv.navigationController != nil){
            self.uv.navigationController?.view.addSubview(bgView)
            self.uv.navigationController?.view.addSubview(changePasswordView)
            
            changePasswordView.tag = Utils.ALERT_DIALOG_CONTENT_TAG
            bgView.tag = Utils.ALERT_DIALOG_BG_TAG
        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(changePasswordView)
        }
        
        if(vPassword == ""){
            self.changePasswordView.currentPassTxtField.isHidden = true
            
            self.changePasswordView.frame.size = CGSize(width: changePasswordView.frame.width, height: changePasswordView.frame.height - 60)
            self.changePasswordView.view.frame = self.changePasswordView.bounds
            self.changePasswordView.passContainerStackViewHeight.constant = self.changePasswordView.passContainerStackViewHeight.constant - 60
        }
    }
    
    func removeView(){
        changePasswordView.frame.origin.y = Application.screenSize.height + 2500
        changePasswordView.removeFromSuperview()
        bgView.removeFromSuperview()
        
        //        self.uv.view.layoutIfNeeded()
        UIApplication.shared.isStatusBarHidden = true
        UIApplication.shared.isStatusBarHidden = false
    }
    
    
    func changePassword(currentPassword:String, password:String){
        if let SITE_TYPE = GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) as? String{
            if(SITE_TYPE == "Demo" && self.userProfileJson.get("vEmail") == "driver@gmail.com"){
                self.generalFunc.setError(uv: uv, title: "", content: self.SITE_TYPE_DEMO_MSG)
                return
            }
        }
        
        DispatchQueue.main.async() {
            self.loadingDialog = NBMaterialLoadingDialog.showLoadingDialogWithText(self.changePasswordView, isCancelable: false, message: (GeneralFunctions()).getLanguageLabel(origValue: "Loading", key: "LBL_LOADING_TXT"))
        }
        
        let parameters = ["type":"updatePassword","UserID": GeneralFunctions.getMemberd(), "CurrentPassword": currentPassword, "pass": password, "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(self.loadingDialog != nil){
                self.loadingDialog.hideDialog()
            }
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.removeView()
                    
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    if(self.handler != nil){
                        self.handler(true)
                    }
                    
                }else{
                    self.changePasswordView.currentPassTxtField.setText(text: "")
                    self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self.uv)
            }
        })
        
        
    }

}
