//
//  OpenGoogleLogin.swift
//  PassengerApp
//
//  Created by NEW MAC on 22/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleSignIn

class OpenGoogleLogin: NSObject, GIDSignInDelegate, GIDSignInUIDelegate {

    typealias CompletionHandler = (_ response:String) -> Void
    
    var uv:UIViewController!
    var window:UIWindow!
    var currGoogleLoginInst:OpenGoogleLogin!
    var loadingDialog:NBMaterialLoadingDialog!
    
    let generalFunc = GeneralFunctions()
    
    init(uv:UIViewController, window:UIWindow) {
        self.uv = uv
        self.window = window
        
        super.init()
    }
    
    func processData(currGoogleLoginInst: OpenGoogleLogin){
        self.currGoogleLoginInst = currGoogleLoginInst
        
        if(InternetConnection.isConnectedToNetwork() == false){
            self.generalFunc.setError(uv: self.uv)
            return
        }
        
//        var configureError: NSError?
//        GGLContext.sharedInstance().configureWithError(&configureError)
//        assert(configureError == nil, "Error configuring Google services: \(String(describing: configureError))")
        GIDSignIn.sharedInstance().clientID = Configurations.getPlistValue(key: "CLIENT_ID", fileName: "GoogleService-Info")
        GIDSignIn.sharedInstance().delegate = self.currGoogleLoginInst
        GIDSignIn.sharedInstance().uiDelegate = self.currGoogleLoginInst
        
        let signInBtn = GIDSignInButton()
        signInBtn.sendActions(for: .touchUpInside)

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
    
    func sign(_ signIn: GIDSignIn!, didSignInFor user: GIDGoogleUser!, withError error: Error!) {
        if (error == nil) {
            // Perform any operations on signed in user here.
            if(user.profile == nil){
                return
            }
            let userId = user.userID                  // For client-side use only!
            _ = user.authentication.idToken // Safe to send to the server
            let fullName = user.profile.name
            let givenName = user.profile.givenName
            let familyName = user.profile.familyName
            let email = user.profile.email
            
            if(email != nil){
                self.executeProcess(vEmail: email!, vFirstName: fullName == nil ? (givenName == nil ? (familyName == nil ? "" : familyName!): givenName!) : fullName!, vLastName: "", vFbId: userId!, vImageURL: user.profile.hasImage ? user.profile.imageURL(withDimension: 1500).absoluteString : "")
            }
            
        } else {
            print("\(error.localizedDescription)")
        }
        
        GIDSignIn.sharedInstance().signOut()
    }
    
    func sign(_ signIn: GIDSignIn!, didDisconnectWith user: GIDGoogleUser!, withError error: Error!) {
        
    }
    
    func sign(inWillDispatch signIn: GIDSignIn!, error: Error!) {
        
    }
    
    func sign(_ signIn: GIDSignIn!, present viewController: UIViewController!) {
        self.uv.present(viewController, animated: true, completion: nil)

    }
    
    func sign(_ signIn: GIDSignIn!, dismiss viewController: UIViewController!) {
        self.uv.dismiss(animated: true, completion: nil)

    }
    
    func executeProcess(vEmail:String, vFirstName:String, vLastName:String, vFbId:String, vImageURL:String){
        
        let userSelectedCurrency = GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String
        let userSelectedLanguage = GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String
        
        let parameters = ["type":"LoginWithFB","vEmail": vEmail, "vFirstName": vFirstName,"vLastName": vLastName, "iFBId": vFbId, "vDeviceType": Utils.deviceType, "eLoginType": "Google", "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "vImageURL": vImageURL]
        
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
        
        let parameters = ["type":"signup","vFirstName": vFirstName, "vLastName": vLastName, "vEmail": vEmail, "vFbId": vFbId, "vDeviceType": Utils.deviceType, "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "eSignUpType": "Google", "vImageURL": vImageURL]
        
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
