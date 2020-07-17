//
//  GetUserData.swift
//  DriverApp
//
//  Created by NEW MAC on 27/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
//import FirebaseCore
//import Firebase
//import FirebaseMessaging

class GetUserData: NSObject {

    var uv:UIViewController!
    var window:UIWindow!
    
    var generalFunc = GeneralFunctions()
    
    init(uv:UIViewController?, window:UIWindow) {
        self.uv = uv
        self.window = window
        if(self.uv == nil){
            self.uv = GeneralFunctions.getVisibleViewController(self.uv)
            
            if(self.uv == nil){
                self.uv = UIViewController()
            }
        }
        super.init()
    }
    
    func getdata(){
        
//        FIRInstanceID.instanceID().delete { (err:Error?) in
//            if err != nil{
//                print(err.debugDescription);
//            } else {
//                print("Token Deleted");
//            }
//        }
    
        let parameters = ["type":"getDetail","UserType": Utils.appUserType, "AppVersion": Utils.applicationVersion(), "vDeviceType": Utils.deviceType, "iUserId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get(Utils.message_str) == "SESSION_OUT"){
                    GeneralFunctions.logOutUser()
                    GeneralFunctions.removeObserver(obj: self.uv)
                    self.generalFunc.setAlertMessage(uv: self.uv, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SESSION_TIME_OUT"), content: self.generalFunc.getLanguageLabel(origValue: "Your session is expired. Please login again.", key: "LBL_SESSION_TIME_OUT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        GeneralFunctions.restartApp(window: self.window!)
                    })
                    
                    return
                }
                if(dataDict.get("Action") == "1"){
                    
                    GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                    GeneralFunctions.postNotificationSignal(key: ConfigPubNub.removeInst_key, obj: self)
                    
                    GeneralFunctions.removeObserver(obj: self.uv)
                    
                    
                    GeneralFunctions.saveValue(key: "GO_ONLINE", value: "1" as AnyObject)
//                    GeneralFunctions.restartApp(window: window)
                    _ = OpenMainProfile(uv: self.uv, userProfileJson: response, window: self.window!)
                    
                }else{
                    GeneralFunctions.removeObserver(obj: self.uv)
                    if(dataDict.get("isAppUpdate") != "" && dataDict.get("isAppUpdate") == "true"){
                        
                        self.generalFunc.setAlertMessage(uv: self.uv, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NEW_UPDATE_AVAIL"), content: self.generalFunc.getLanguageLabel(origValue: "New update is available to download. Downloading the latest update, you will get latest features, improvements and bug fixes.", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Update", key: "LBL_UPDATE"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), completionHandler: { (btnClickedIndex) in
                            
                            if(btnClickedIndex == 0){
                                UIApplication.shared.openURL(URL(string: "itms-apps://itunes.apple.com/app/bars/id\(CommonUtils.appleAppId)")!)
                            }
                            
                            GeneralFunctions.restartApp(window: self.window!)
                            
                        })
                        return
                    }else{
                        
                        if(dataDict.get(Utils.message_str) == "LBL_CONTACT_US_STATUS_NOTACTIVE_DRIVER" || dataDict.get(Utils.message_str) == "LBL_ACC_DELETE_TXT"){
                            GeneralFunctions.logOutUser()
                            self.generalFunc.setAlertMessage(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                                
                                GeneralFunctions.restartApp(window: self.window!)
                            })
                            
                            return
                        }
                        self.generalFunc.setAlertMessage(uv: self.uv, title: self.generalFunc.getLanguageLabel(origValue: "Error", key: "LBL_ERROR_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "Please try again.", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            GeneralFunctions.restartApp(window: self.window!)
                        })
                    }
                    
                }
                
            }else{
                self.generalFunc.setAlertMessage(uv: self.uv, title: self.generalFunc.getLanguageLabel(origValue: "Error", key: "LBL_ERROR_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "Please try again.", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    GeneralFunctions.restartApp(window: self.window!)
                })
                //                self.generalFunc.setError(self, completionHandler: { (isBtnClicked) -> Void in
                //                    self.findTripData()
                //                })
            }
        })
        
    }

}
