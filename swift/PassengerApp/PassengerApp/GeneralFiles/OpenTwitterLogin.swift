//
//  OpenTwitterLogin.swift
//  PassengerApp
//
//  Created by NEW MAC on 22/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenTwitterLogin: NSObject {

    typealias CompletionHandler = (_ response:String) -> Void
    
    var uv:UIViewController!
    var window:UIWindow!
    var currTwitterLoginInst:OpenTwitterLogin!
    var loadingDialog:NBMaterialLoadingDialog!
    
    let generalFunc = GeneralFunctions()
    
    init(uv:UIViewController, window:UIWindow) {
        self.uv = uv
        self.window = window
        
        super.init()
    }
    
    func processData(currTwitterLoginInst: OpenTwitterLogin){
        self.currTwitterLoginInst = currTwitterLoginInst
        
        if(InternetConnection.isConnectedToNetwork() == false){
            self.generalFunc.setError(uv: self.uv)
            return
        }
        
        Twitter.sharedInstance().start(withConsumerKey: Configurations.getInfoPlistValue(key: "TWITTER_CONSUMER_KEY"), consumerSecret: Configurations.getInfoPlistValue(key: "TWITTER_CONSUMER_SECRET_KEY"))
        
//        Twitter.sharedInstance().logIn(with: self.uv, methods: .webBased)
        Twitter.sharedInstance().logIn  {
            (session, error) -> Void in
            if (session != nil) {
                
                self.checkForEmail(session: session!)
                
            }else {
                self.generalFunc.setError(uv: self.uv, title: "", content: error != nil ? (error!.localizedDescription) : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRY_AGAIN_TXT"))
            }
        }
        
    }
    
    func checkForEmail(session:TWTRSession){
        let client = TWTRAPIClient.withCurrentUser()
        let request = client.urlRequest(withMethod: "GET", url: "https://api.twitter.com/1.1/account/verify_credentials.json?include_email=true", parameters: ["include_email": "true", "skip_status": "true"], error: nil)
        
        client.sendTwitterRequest(request) { (response, data, error) in
            
            if(error != nil){
                self.generalFunc.setError(uv: self.uv, title: "", content: error!.localizedDescription)
            }else{
                var dataString = ""
                
                if(data == nil){
                    dataString = ""
                }else{
                    dataString =  String(data: data!, encoding: String.Encoding.utf8)!
                }
                
                Utils.printLog(msgData: "TwitterData::\(dataString)")
                
                if(dataString != ""){
                    Twitter.sharedInstance().sessionStore.logOutUserID(session.userID)
                    
                    let dataDict = dataString.getJsonDataDict()
                    
                    let profileImageURL = dataDict.get("profile_image_url_https").replace("_normal", withString: "")

//                    Utils.printLog(msgData: "profileImageURL::\(profileImageURL)")
                    self.executeProcess(vEmail: dataDict.get("email"), vFirstName: dataDict.get("name"), vLastName: "", vFbId: dataDict.get("id"), vImageURL: profileImageURL)
                }else{
                    self.generalFunc.setError(uv: self.uv)
                }
                
            }
            
            
        }
       
//        client.requestEmail { email, error in
//            if (email != nil) {
//                //                print("signed in as \(session.userName)");
////                print("Email:\(email)")
//
//                Twitter.sharedInstance().sessionStore.logOutUserID(session.userID)
//
//                self.executeProcess(vEmail: email!, vFirstName: session.userName, vLastName: "", vFbId: session.userID)
//
//            } else {
////                print("error: \(error?.localizedDescription)");
//
//                self.executeProcess(vEmail: "", vFirstName: session.userName, vLastName: "", vFbId: session.userID)
//
//            }
//        }
    }
    
    func executeProcess(vEmail:String, vFirstName:String, vLastName:String, vFbId:String, vImageURL:String){
        
        let userSelectedCurrency = GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String
        let userSelectedLanguage = GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String
        
        let parameters = ["type":"LoginWithFB","vEmail": vEmail, "vFirstName": vFirstName,"vLastName": vLastName, "iFBId": vFbId, "vDeviceType": Utils.deviceType, "eLoginType": "Twitter", "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "vImageURL": vImageURL]
        
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
        
        let parameters = ["type":"signup","vFirstName": vFirstName, "vLastName": vLastName, "vEmail": vEmail, "vFbId": vFbId, "vDeviceType": Utils.deviceType, "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "eSignUpType": "Twitter", "vImageURL": vImageURL]
        
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
