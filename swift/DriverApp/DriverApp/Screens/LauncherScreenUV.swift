//
//  LauncherScreenUV.swift
//  DriverApp
//
//  Created by NEW MAC on 04/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

class LauncherScreenUV: UIViewController, OnLocationUpdateDelegate {

    @IBOutlet weak var indicatorView: DGActivityIndicatorView!
    @IBOutlet weak var bgImgView: UIImageView!
    let generalFunc = GeneralFunctions()
    
    var getLocation:GetLocation!
    var locationDialog:MTDialog!

    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
    
        super.viewDidLoad()
        
        self.view.addSubview(self.generalFunc.loadView(nibName: "LauncherScreenDesign", uv: self))
        
//        self.bgImgView.image = UIImage(named: "ic_launch")
        self.bgImgView.image = Utils.appLaunchImage()
        
        indicatorView.type = .ballPulse
        indicatorView.tintColor = UIColor.UCAColor.AppThemeTxtColor
        indicatorView.size = 50
        indicatorView.startAnimating()
        
        continueProcess()
        
    }
//    isOtherButton
    func continueProcess(){
        
        if(InternetConnection.isConnectedToNetwork() == false){
            
            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "No Internet Connection", key: "LBL_NO_INTERNET_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                
                self.continueProcess()
            })
            
            return
        }
        
        if(Configurations.isUserLoggedIn() == false){
            downloadData()
        }else{
            autoLogin()
        }
    }

    func downloadData(){
        var parameters = ["type":"generalConfigData","UserType": Utils.appUserType, "AppVersion": Utils.applicationVersion()]
        
        if(GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) != nil){
            parameters["vLang"] = (GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String)
        }
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
//                    if(GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) == nil || (GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String) == dataDict.getObj("DefaultLanguageValues").get("vCode")){
                    
                        GeneralFunctions.saveValue(key: Utils.LANGUAGE_CODE_KEY, value: dataDict.getObj("DefaultLanguageValues").get("vCode") as AnyObject)
                        GeneralFunctions.saveValue(key: Utils.languageLabelsKey, value: dataDict.getObj("LanguageLabels"))
                        GeneralFunctions.saveValue(key: Utils.DEFAULT_LANGUAGE_TITLE_KEY, value: dataDict.getObj("DefaultLanguageValues").get("vTitle") as AnyObject)

                        GeneralFunctions.saveValue(key: Utils.LANGUAGE_IS_RTL_KEY, value: dataDict.getObj("DefaultLanguageValues").get("eType") as AnyObject)

                        GeneralFunctions.saveValue(key: Utils.GOOGLE_MAP_LANGUAGE_CODE_KEY, value: dataDict.getObj("DefaultLanguageValues").get("vGMapLangCode") as AnyObject)

                        
                        Configurations.setAppLocal()
//                    }
                    
                    if(GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) == nil || (GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String) == dataDict.getObj("DefaultCurrencyValues").get("vName")){
                        GeneralFunctions.saveValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY, value: dataDict.getObj("DefaultCurrencyValues").get("vName") as AnyObject)

                    }
                    
                    GeneralFunctions.saveValue(key: Utils.DEFAULT_COUNTRY_KEY, value: dataDict.get("vDefaultCountry") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY, value: dataDict.get("vDefaultCountryCode") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.DEFAULT_PHONE_CODE_KEY, value: dataDict.get("vDefaultPhoneCode") as AnyObject)
                    
                    GeneralFunctions.saveValue(key: Utils.FACEBOOK_LOGIN_KEY, value: dataDict.get(Utils.FACEBOOK_LOGIN_KEY) as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.GOOGLE_LOGIN_KEY, value: dataDict.get(Utils.GOOGLE_LOGIN_KEY) as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.TWITTER_LOGIN_KEY, value: dataDict.get( Utils.TWITTER_LOGIN_KEY) as AnyObject)
                    
                    GeneralFunctions.saveValue(key: Utils.FACEBOOK_APPID_KEY, value: dataDict.get("FACEBOOK_APP_ID") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.LINK_FORGET_PASS_KEY, value: dataDict.get("LINK_FORGET_PASS_PAGE_DRIVER") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY, value: dataDict.get("MOBILE_VERIFICATION_ENABLE") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.LANGUAGE_LIST_KEY, value: dataDict.getArrObj("LIST_LANGUAGES"))
                    GeneralFunctions.saveValue(key: Utils.CURRENCY_LIST_KEY, value: dataDict.getArrObj("LIST_CURRENCY"))

                    GeneralFunctions.saveValue(key: Utils.REFERRAL_SCHEME_ENABLE, value: dataDict.get("REFERRAL_SCHEME_ENABLE") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.SITE_TYPE_KEY, value: dataDict.get("SITE_TYPE") as AnyObject)
                    
                    IQKeyboardManager.shared().toolbarDoneBarButtonItemText = (GeneralFunctions()).getLanguageLabel(origValue: "Done", key: "LBL_DONE")

                    if(dataDict.get("SERVER_MAINTENANCE_ENABLE").uppercased() == "YES"){
                        
                        let maintenancePageUV = GeneralFunctions.instantiateViewController(pageName: "MaintenancePageUV") as! MaintenancePageUV
                        
                        GeneralFunctions.changeRootViewController(window: Application.window!, viewController: maintenancePageUV)
                        
                    }else{
                        DispatchQueue.main.asyncAfter(deadline: .now() + 2) {
                            let appLoginUv = GeneralFunctions.instantiateViewController(pageName: "AppLoginUV") as! AppLoginUV
                            
                            GeneralFunctions.changeRootViewController(window: Application.window!, viewController: appLoginUv)
                        }
                    }
                    
                }else{
                    
                    if(dataDict.get("isAppUpdate") != "" && dataDict.get("isAppUpdate") == "true"){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NEW_UPDATE_AVAIL"), content: self.generalFunc.getLanguageLabel(origValue: "New update is available to download. Downloading the latest update, you will get latest features, improvements and bug fixes.", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Update", key: "LBL_UPDATE"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), completionHandler: { (btnClickedIndex) in
                            
                            if(btnClickedIndex == 0){
                                UIApplication.shared.openURL(URL(string: "itms-apps://itunes.apple.com/app/bars/id\(CommonUtils.appleAppId)")!)
                            }
                            
                            self.continueProcess()
                            
                        })
                        return
                    }
                    
                    self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "Error", key: "LBL_ERROR_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "Please try again.", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        self.continueProcess()
                    })
                }
                
            }else{
                self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "Error", key: "LBL_ERROR_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "Please try again.", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    self.continueProcess()
                })
                //                self.generalFunc.setError(self, completionHandler: { (isBtnClicked) -> Void in
                //                    self.findTripData()
                //                })
            }
        })
    }
    
    func appInForground(){
        self.autoLogin()
    }
    
    func addBackgroundObserver(){
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func autoLogin(){
//        if(locationDialog != nil){
//            locationDialog.disappear()
//            locationDialog = nil
//        }
//        
//        if(GeneralFunctions.hasLocationEnabled() == false){
//            
//            getLocation = GetLocation(uv: self, isContinuous: false)
//            getLocation.buildLocManager(locationUpdateDelegate: self)
//            
//            locationDialog = self.generalFunc.setAlertMessageWithReturnDialog(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_IPHONE_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "Settings", key: "LBL_SETTINGS"), completionHandler: { (btnClickedIndex) in
//                
//                if(btnClickedIndex == 1){
//                    if (CLLocationManager.locationServicesEnabled()  == false) {
//                        if let url = URL(string: "App-Prefs:root=Privacy&path=LOCATION") {
//                            // If general location settings are disabled then open general location settings
//                            UIApplication.shared.openURL(url)
//                        }
//                    } else {
//                        if let url = URL(string: UIApplicationOpenSettingsURLString) {
//                            // If general location settings are enabled then open location settings for the app
//                            UIApplication.shared.openURL(url)
//                        }
//                    }
//                }
//                
//                self.autoLogin()
//            })
//            
//            self.addBackgroundObserver()
//
//            
//            return
//        }
        
        
        var parameters = ["type":"getDetail","UserType": Utils.appUserType, "AppVersion": Utils.applicationVersion(), "vDeviceType": Utils.deviceType, "iUserId": GeneralFunctions.getMemberd()]
        
        if(GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) != nil){
            parameters["vLang"] =  "\(GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String)"
        }
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            GeneralFunctions.removeObserver(obj: self)

            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get(Utils.message_str) == "SESSION_OUT"){
                    GeneralFunctions.logOutUser()
                    self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SESSION_TIME_OUT"), content: self.generalFunc.getLanguageLabel(origValue: "Your session is expired. Please login again.", key: "LBL_SESSION_TIME_OUT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        self.continueProcess()
                    })
                    
                    return
                }
                if(dataDict.get("Action") == "1"){
                    
                    _ = SetUserData(uv: self, userProfileJson: dataDict, isStoreUserId: false)
                    
                    if(dataDict.getObj(Utils.message_str).get("SERVER_MAINTENANCE_ENABLE").uppercased() == "YES"){

                        let maintenancePageUV = GeneralFunctions.instantiateViewController(pageName: "MaintenancePageUV") as! MaintenancePageUV

                        GeneralFunctions.changeRootViewController(window: Application.window!, viewController: maintenancePageUV)

                    }else{
                        DispatchQueue.main.asyncAfter(deadline: .now() + 2) {
                            
                            let window = UIApplication.shared.delegate!.window!
                            _ = OpenMainProfile(uv: self, userProfileJson: response, window: window!)
                        }
                    }
                    
                }else{
                    
                    if(dataDict.get("isAppUpdate") != "" && dataDict.get("isAppUpdate") == "true"){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NEW_UPDATE_AVAIL"), content: self.generalFunc.getLanguageLabel(origValue: "New update is available to download. Downloading the latest update, you will get latest features, improvements and bug fixes.", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Update", key: "LBL_UPDATE"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), completionHandler: { (btnClickedIndex) in
                            
                            if(btnClickedIndex == 0){
                                UIApplication.shared.openURL(URL(string: "itms-apps://itunes.apple.com/app/bars/id\(CommonUtils.appleAppId)")!)
                            }
                            
                            self.continueProcess()
                            
                        })
                        return
                    }else{
                    
                        if(dataDict.get(Utils.message_str) == "LBL_CONTACT_US_STATUS_NOTACTIVE_DRIVER" || dataDict.get(Utils.message_str) == "LBL_ACC_DELETE_TXT"){
                            GeneralFunctions.logOutUser()
//                            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
//
//                                self.continueProcess()
//                            })

                            self.openAccStatusInvalid(dataDict: dataDict)
                            
                            return
                        }
                        self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "Error", key: "LBL_ERROR_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "Please try again.", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            self.continueProcess()
                        })
                    }
                    
                }
                
            }else{
                self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "Error", key: "LBL_ERROR_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "Please try again.", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    self.continueProcess()
                })
                //                self.generalFunc.setError(self, completionHandler: { (isBtnClicked) -> Void in
                //                    self.findTripData()
                //                })
            }
        })
    }
    
    func openAccStatusInvalid(dataDict:NSDictionary){
        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT"), completionHandler: { (btnClickedIndex) in
            
            if(btnClickedIndex == 0){
                self.continueProcess()
            }else{
                let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                self.pushToNavController(uv: contactUsUv)
                self.openAccStatusInvalid(dataDict: dataDict)
            }
        })
    }
}
