//
//  OpenFbLogin.swift
//  DriverApp
//
//  Created by NEW MAC on 22/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenFbLogin: NSObject, FBSDKLoginButtonDelegate {

    typealias CompletionHandler = (_ response:String) -> Void
    
    var uv:UIViewController!
    var window:UIWindow!
    var currFbLoginInst:OpenFbLogin!
    var loadingDialog:NBMaterialLoadingDialog!
    
    let generalFunc = GeneralFunctions()
    
    init(uv:UIViewController, window:UIWindow) {
        self.uv = uv
        self.window = window
        
        super.init()
    }
    
    func processData(openFbLoginInst: OpenFbLogin){
        self.currFbLoginInst = openFbLoginInst
        
        if(InternetConnection.isConnectedToNetwork() == false){
            self.generalFunc.setError(uv: self.uv)
            return
        }
        
        let fbLoginBtn = FBSDKLoginButton()
        fbLoginBtn.readPermissions = ["public_profile", "email", "user_friends"]
//        fbLoginBtn.loginBehavior = .native
        fbLoginBtn.delegate = self.currFbLoginInst
        fbLoginBtn.sendActions(for: .touchUpInside)
        
    }
    
    func logOutFromFacebook(){
        FBSDKLoginManager().logOut()
        FBSDKAccessToken.current()
        let loginManager = FBSDKLoginManager()
        loginManager.logOut()
    }
    
    func loginButton(_ loginButton: FBSDKLoginButton!, didCompleteWith result: FBSDKLoginManagerLoginResult!, error: Error!) {
        
        if ((error) != nil)
        {
            // Process error
        }
        else if result.isCancelled {
            // Handle cancellations
        }
        else {
//            if result.grantedPermissions.contains("email")
//            {
                getUserData()
//            }
        }
    }
    
    func loginButtonDidLogOut(_ loginButton: FBSDKLoginButton!) {
//                print("User Logged Out")
    }
    
    
    func openLoader(){
        DispatchQueue.main.async() {
            self.loadingDialog = NBMaterialLoadingDialog.showLoadingDialogWithText(self.uv.view, isCancelable: false, message: (GeneralFunctions()).getLanguageLabel(origValue: "Loading", key: "LBL_LOADING_TXT"))
        }
    }
    
    func closeLoader(){
        DispatchQueue.main.async() {
            if(self.loadingDialog != nil){
                self.loadingDialog.hideDialog()
            }
        }
    }
    func getUserData()
    {
        
        self.openLoader()
        
        let graphRequest : FBSDKGraphRequest = FBSDKGraphRequest(graphPath: "me", parameters: ["fields":"id,email,first_name,last_name,name, picture.width(2048).height(2048)"])
        graphRequest.start(completionHandler: { (connection, result, error) -> Void in

            self.closeLoader()
            
            DispatchQueue.main.async() {
                
                if ((error) != nil){
                    // Process error
                    print("Error: \(String(describing: error))")
                }else{
                    
                    if let data = result as? NSDictionary {
                        
                        var vImageURL = data.getObj("picture").getObj("data").get("url")
                        
                        if(vImageURL == ""){
                            vImageURL = "https://graph.facebook.com/\(data.get("id"))/picture?type=large"
                        }
//                        Utils.printLog(msgData: "vImageURL::\(vImageURL)")
                        self.executeProcess(vEmail: data.get("email"), vFirstName: data.get("first_name"), vLastName: data.get("last_name"), vFbId: data.get("id"), vImageURL: vImageURL)

                    }
                }
            }
            
            self.logOutFromFacebook()
        })
    }
    
    func executeProcess(vEmail:String, vFirstName:String, vLastName:String, vFbId:String, vImageURL:String){
    
        let userSelectedCurrency = GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String
        let userSelectedLanguage = GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String
        
        
        let parameters = ["type":"LoginWithFB","vEmail": vEmail, "vFirstName": vFirstName,"vLastName": vLastName, "iFBId": vFbId, "vDeviceType": Utils.deviceType, "UserType": Utils.appUserType, "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "vImageURL": vImageURL]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    _ = SetUserData(uv: self.uv, userProfileJson: dataDict, isStoreUserId: true)
                    
                    let window = UIApplication.shared.delegate!.window!
                    _ = OpenMainProfile(uv: self.uv, userProfileJson: response, window: window!)
                    
                }else{
                    if(dataDict.get(Utils.message_str) == "DO_REGISTER"){
                        
                        self.registerUser(vEmail: vEmail, vFirstName: vFirstName, vLastName: vLastName, vFbId: vFbId, vImageURL: vImageURL)
                        
                    }else{
                        self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                }
                
            }else{
                self.generalFunc.setError(uv: self.uv)
            }
        })
        
    }
    
    func registerUser(vEmail:String, vFirstName:String, vLastName:String, vFbId:String, vImageURL:String){
        
        let userSelectedCurrency = GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String
        let userSelectedLanguage = GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String
        
        let parameters = ["type":"signup","vFirstName": vFirstName, "vLastName": vLastName, "vEmail": vEmail, "vFbId": vFbId, "vDeviceType": Utils.deviceType, "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "eSignUpType": "Facebook", "UserType": Utils.appUserType, "vImageURL": vImageURL]
        
//        , "vPhone": "", "vPassword": "", "PhoneCode": "", "CountryCode": "", "vInviteCode": ""
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    _ = SetUserData(uv: self.uv, userProfileJson: dataDict, isStoreUserId: true)
                    
                    let window = UIApplication.shared.delegate!.window!
                    _ = OpenMainProfile(uv: self.uv, userProfileJson: response, window: window!)
                    
                }else{
                    self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self.uv)
            }
        })
    }
    
}
