//
//  ExeServerUrl.swift
//  DriverApp
//
//  Created by NEW MAC on 24/12/16.
//  Copyright © 2016 BBCS. All rights reserved.
//

import UIKit


class ExeServerUrl: NSObject {
    
    typealias CompletionHandler = (_ response:String) -> Void
    
    var dict_data:[String: String]?
    var isOpenLoader = true
    
    var loadingDialog:NBMaterialLoadingDialog!
    
    var currentView:UIView!
    
    var isDeviceTokenGenerate = false
    
    var currentPostString = ""
    var currCompletionHandler:CompletionHandler!
    var currRequest:NSMutableURLRequest!
    
    var currInstance:ExeServerUrl!
    
    var currentTask:URLSessionDataTask!
    
    init(dict_data: [String: String], currentView:UIView, isOpenLoader:Bool) {
        self.dict_data = dict_data
        self.isOpenLoader = isOpenLoader
        self.currentView = currentView
        super.init()
    }
    
    init(dict_data: [String: String], currentView:UIView, isOpenLoader:Bool, isDeviceTokenGenerate:Bool) {
        self.dict_data = dict_data
        self.isOpenLoader = isOpenLoader
        self.currentView = currentView
        self.isDeviceTokenGenerate = isDeviceTokenGenerate
        super.init()
    }
    
    init(dict_data: [String: String], currentView:UIView) {
        self.dict_data = dict_data
        self.currentView = currentView
        super.init()
    }
    
    static func getInstance(dict_data: [String: String], currentView:UIView, isOpenLoader:Bool, isDeviceTokenGenerate:Bool) -> ExeServerUrl{
        
        return ExeServerUrl(dict_data: dict_data, currentView: currentView, isOpenLoader: isOpenLoader, isDeviceTokenGenerate: isDeviceTokenGenerate)
    }

    func setDeviceTokenGenerate(isDeviceTokenGenerate:Bool){
        self.isDeviceTokenGenerate = isDeviceTokenGenerate
    }
    
    func executePostProcess(completionHandler: @escaping CompletionHandler) {
        
        var firstParam = true
        
        if(isOpenLoader && currentView != nil){
            DispatchQueue.main.async() {
                self.loadingDialog = NBMaterialLoadingDialog.showLoadingDialogWithText(self.currentView, isCancelable: false, message: (GeneralFunctions()).getLanguageLabel(origValue: "Loading", key: "LBL_LOADING_TXT"))
            }
        }
        
        let request = NSMutableURLRequest(url: NSURL(string: CommonUtils.webservice_path)! as URL)
        
        request.httpMethod = "POST"
        
        var postString = ""
        
        for (key, value) in dict_data! {
            if(firstParam == true){
                postString = "\(key)=\(value)"
                firstParam = false
            }else{
                postString = "\(postString)&\(key)=\(value)"
            }
        }
        
        postString = postString + "&Platform=IOS"

        if(isDeviceTokenGenerate == false){
            continuePostProcess(postString: postString, request: request, completionHandler: completionHandler)
        }else{
            self.currentPostString = postString
            self.currRequest = request
            self.currCompletionHandler = completionHandler
            
            NotificationCenter.default.addObserver(currInstance!, selector: #selector(currInstance!.apnIdReceivedCallback(sender:)), name: NSNotification.Name(rawValue: Utils.apnIDNotificationKey), object: nil)
            
            GeneralFunctions.registerRemoteNotification()
        }
        
    }
    
    func apnIdReceivedCallback(sender: NSNotification){
        let userInfo = sender.userInfo
        let apnId_str = userInfo!["body"] as! String
//        let fcmDeviceToken = userInfo!["FCMToken"] as! String
        
        GeneralFunctions.saveValue(key: Utils.deviceTokenKey, value: apnId_str as AnyObject)
        GeneralFunctions.removeObserver(obj: self)
        GeneralFunctions.removeObserver(obj: currInstance!)
        
        currentPostString = currentPostString + "&vDeviceToken=\(apnId_str)"
//        &vFirebaseDeviceToken=\(fcmDeviceToken)
        continuePostProcess(postString: self.currentPostString, request: self.currRequest, completionHandler: self.currCompletionHandler)
    }
    
    private func continuePostProcess(postString:String, request: NSMutableURLRequest, completionHandler: @escaping CompletionHandler){
        
        let postString = postString + "&tSessionId=\(GeneralFunctions.getSessionId())&GeneralMemberId=\(GeneralFunctions.getMemberd())&GeneralUserType=\(Utils.appUserType)&GeneralDeviceType=\(Utils.deviceType)&GeneralAppVersion=\(Utils.applicationVersion())&vTimeZone=\(DateFormatter().timeZone.identifier)&vUserDeviceCountry=\(Utils.getDeviceCountryCode())"
//        , "TimeZone": "\(DateFormatter().timeZone.identifier)"
        Utils.printLog(msgData: "postString:\(postString)")
        request.httpBody = postString.data(using: String.Encoding.utf8)
        
        let task = URLSession.shared.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            
            var dataString = ""
            
            if(data == nil){
                dataString = ""
            }else{
                dataString =  String(data: data!, encoding: String.Encoding.utf8)!
            }
            
            DispatchQueue.main.async() {
                if(self.loadingDialog != nil){
                    self.loadingDialog.hideDialog()
                }
                completionHandler(dataString.trim())
            }
            
        })
        
        task.resume()
        
        self.currentTask = task
    }
    
    func executeGetProcess(completionHandler: @escaping CompletionHandler, url:String) {
        
        if(isOpenLoader && currentView != nil){
            DispatchQueue.main.async() {
                self.loadingDialog = NBMaterialLoadingDialog.showLoadingDialogWithText(self.currentView, isCancelable: false, message: (GeneralFunctions()).getLanguageLabel(origValue: "Loading", key: "LBL_LOADING_TXT"))
            }
        }
        
        let request = NSMutableURLRequest(url: NSURL(string: url.addingPercentEncoding(withAllowedCharacters: .urlFragmentAllowed)!)! as URL)

        Utils.printLog(msgData: "request:::\(String(describing: request.url))")
        request.httpMethod = "GET"
        
        let task = URLSession.shared.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            var dataString = ""
            
            if(data == nil){
                dataString = ""
            }else{
                dataString =  String(data: data!, encoding: String.Encoding.utf8)!
            }
            
            DispatchQueue.main.async() {
                if(self.loadingDialog != nil){
                    self.loadingDialog.hideDialog()
                }
                
                completionHandler(dataString.trim())
            }
            
        })
        
        task.resume()
        
        self.currentTask = task
        
    }
    
    func uploadImage(image:UIImage, completionHandler: @escaping CompletionHandler){
        let boundary = GeneralFunctions.generateBoundaryString()
        
        let request = NSMutableURLRequest(url: NSURL(string: CommonUtils.webservice_path)! as URL)
        request.httpMethod = "POST"
        request.setValue("multipart/form-data; boundary=\(boundary)", forHTTPHeaderField: "Content-Type")

        let imageData = UIImageJPEGRepresentation(image, 0.8)
        
        if(imageData==nil)  {
            DispatchQueue.main.async() {
                completionHandler("")
            }
            return
        }
//        &tSessionId=\(GeneralFunctions.getSessionId())&GeneralMemberId=\(GeneralFunctions.getMemberd())&GeneralUserType=\(Utils.appUserType)&GeneralDeviceType=\(Utils.deviceType)&GeneralAppVersion=\(Utils.applicationVersion())"
        dict_data?["tSessionId"] = "\(GeneralFunctions.getSessionId())"
        dict_data?["GeneralMemberId"] = "\(GeneralFunctions.getMemberd())"
        dict_data?["GeneralUserType"] = "\(Utils.appUserType)"
        dict_data?["GeneralDeviceType"] = "\(Utils.deviceType)"
        dict_data?["GeneralAppVersion"] = "\(Utils.applicationVersion())"
        dict_data?["vTimeZone"] = "\(DateFormatter().timeZone.identifier)"
        
        request.httpBody = GeneralFunctions.createBodyWithParameters(dict_data, filePathKey: "vImage", imageDataKey: imageData!, boundary: boundary)
        
        if(isOpenLoader && currentView != nil){
            DispatchQueue.main.async() {
                self.loadingDialog = NBMaterialLoadingDialog.showLoadingDialogWithText(self.currentView, isCancelable: false, message: (GeneralFunctions()).getLanguageLabel(origValue: "Loading", key: "LBL_LOADING_TXT"))
            }
        }
        
        let task = URLSession.shared.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            
            var dataString = ""
            
            if(data == nil){
                dataString = ""
            }else{
                dataString =  String(data: data!, encoding: String.Encoding.utf8)!
            }
            
            DispatchQueue.main.async() {
                if(self.loadingDialog != nil){
                    self.loadingDialog.hideDialog()
                }
                completionHandler(dataString.trim())
            }
            
        })
        
        task.resume()

        
        self.currentTask = task
    }
    
    func cancel(){
        if(currentTask != nil){
            self.currentTask.cancel()
        }
    }

}
