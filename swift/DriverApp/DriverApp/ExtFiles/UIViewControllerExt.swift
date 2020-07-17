//
//  UIViewControllerExt.swift
//  DriverApp
//
//  Created by NEW MAC on 08/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
import GoogleMaps

extension UIViewController {
    
    
    func pushToNavController(uv:UIViewController){
        
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if (Application.window != nil)
        {
            Application.window?.endEditing(true)
        }
        else
        {
            uv.view.endEditing(true)
        }
        
        GeneralFunctions.removeAllAlertViewFromNavBar(uv: self)
        
        self.closeDrawerMenu()
        
        DispatchQueue.main.async() {
            if(self.navigationController == nil){
                let navController = UINavigationController(rootViewController: uv)
                navController.navigationBar.isTranslucent = false
                self.present(navController, animated: true, completion: nil)
            }else{
                self.navigationController?.pushViewController(uv, animated: true)
            }
        }
    }
    
    func pushToNavController(uv:UIViewController, isDirect:Bool){
        
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if (Application.window != nil)
        {
            Application.window?.endEditing(true)
        }
        else
        {
            uv.view.endEditing(true)
        }
        GeneralFunctions.removeAllAlertViewFromNavBar(uv: self)
        
        self.closeDrawerMenu()
        DispatchQueue.main.async() {
            let navController = UINavigationController(rootViewController: uv)
            navController.navigationBar.isTranslucent = false
            self.present(navController, animated: true, completion: nil)
        }
    }
    
    func addBackBarBtn(){
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        var backImg = UIImage(named: "ic_nav_bar_back")!
        
        if(Configurations.isRTLMode()){
            backImg = backImg.imageRotatedByDegrees(oldImage: backImg, deg: 180)
        }
        
        let leftButton: UIBarButtonItem = UIBarButtonItem(image: backImg, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.closeCurrentScreen))
        
        self.navigationItem.leftBarButtonItem = leftButton;
    }
    
    func closeCurrentScreen(){
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if (Application.window != nil)
        {
            Application.window?.endEditing(true)
        }
        else
        {
            self.view.endEditing(true)
        }
        GeneralFunctions.removeAllAlertViewFromNavBar(uv: self)
        
        if(self.navigationController == nil || self.navigationController?.viewControllers.count == 1){
            self.dismiss(animated: true, completion: nil)
        }else{
            self.navigationController?.popViewController(animated: true)
        }
    }
    
    func closeCurrentScreenAnimConfig(isAnimated:Bool){
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        GeneralFunctions.removeAllAlertViewFromNavBar(uv: self)
        
        if (self.navigationController == nil || self.navigationController?.viewControllers.count == 1) {
            self.dismiss(animated: isAnimated, completion: nil)
        }else{
            self.navigationController?.popViewController(animated: isAnimated)
        }
    }
    
    func addActivityIndicator() -> UIActivityIndicatorView{
        let loader_IV = UIActivityIndicatorView(frame: CGRect(x: 0, y: 0, width: 50, height: 50))
        
        loader_IV.hidesWhenStopped = false
        loader_IV.startAnimating()
        loader_IV.activityIndicatorViewStyle = UIActivityIndicatorViewStyle.whiteLarge
        loader_IV.color = UIColor.black
        loader_IV.autoresizingMask = [.flexibleBottomMargin, .flexibleTopMargin, .flexibleLeftMargin, .flexibleRightMargin]
        loader_IV.center = self.view.center;
        self.view.addSubview(loader_IV)
        
        return loader_IV
    }
    
    func configureRTLView(){
        let languageType = UserDefaults.standard.value(forKey: Utils.LANGUAGE_IS_RTL_KEY)
        
        if(languageType != nil){
            let languageType_str = languageType as! String
            
            if(languageType_str == Utils.DATABASE_RTL_STR){
                UIView.appearance().semanticContentAttribute = .forceRightToLeft
            }else{
                UIView.appearance().semanticContentAttribute = .forceLeftToRight
            }
        }else{
            UIView.appearance().semanticContentAttribute = .forceLeftToRight
        }
    }
    
    var className: String {
        return NSStringFromClass(self.classForCoder).components(separatedBy: ".").last!;
    }
    
    func getPubNubConfig()->String{
        if(GeneralFunctions.getValue(key: Utils.ENABLE_PUBNUB_KEY) != nil){
            return GeneralFunctions.getValue(key: Utils.ENABLE_PUBNUB_KEY) as! String
        }
        return ""
    }
    
    func addNotifyOnPassengerRequested(){
        removeNotifyOnPassengerRequested()
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.passengerRequestArrived(sender:)), name: NSNotification.Name(rawValue: Utils.passengerRequestArrived), object: nil)
    }
    
    func removeNotifyOnPassengerRequested(){
        NotificationCenter.default.removeObserver(self,name: NSNotification.Name(rawValue: Utils.passengerRequestArrived), object: nil)
    }
    
    func passengerRequestArrived(sender:NSNotification){
        
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        //        removeNotifyOnPassengerRequested()
        
        Utils.resetAppNotifications()
        Utils.closeKeyboard(uv: self)
        
        let userInfo = sender.userInfo
        let jsonDataMsg = userInfo!["body"] as! String
        
        let result = jsonDataMsg.getJsonDataDict()
        
        let msg_str = result.get("Message")
        if(msg_str == "DestinationAdded"){
            
            //            (self as! ActiveTripUV).passengerRequestArrive(sender)
            
        }else if(msg_str != ""){
            
            let msgCode = result.get("MsgCode")
            
            if(msgCode != ""){
                let codeValue = GeneralFunctions.getValue(key: Utils.DRIVER_REQ_CODE_PREFIX_KEY + msgCode)
                
                if(codeValue == nil){
                    
//                    LocalNotification.dispatchlocalNotification(with: "", body: (GeneralFunctions()).getLanguageLabel(origValue: "", key: "LBL_TRIP_USER_WAITING"), at: Date().addedBy(seconds: 0), onlyInBackground: true)
                    
                    GeneralFunctions.saveValue(key: Utils.DRIVER_REQ_CODE_PREFIX_KEY + msgCode, value: "\(Utils.currentTimeMillis())" as AnyObject)
                    
                    
                    let cabRequestedUV = GeneralFunctions.instantiateViewController(pageName: "CabRequestedUV") as! CabRequestedUV
                    cabRequestedUV.passengerJsonDetail_dict = result
                    //                    self.pushToNavController(uv: cabRequestedUV)
                    cabRequestedUV.initializedMiliSeconds = Utils.currentTimeMillis()
                    
                    self.closeDrawerMenu()
                    
                    GeneralFunctions.removeAllAlertViewFromNavBar(uv: self)
                    
                    self.navigationController?.pushViewController(cabRequestedUV, animated: false)
                    
                }
                
            }
            
        }
    }
    
    func observeCancelTripRequest(){
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.requestCancelTrip(sender:)), name: NSNotification.Name(rawValue: Utils.tripRequestCanceled), object: nil)
    }
    
    func observeTripDestionationAdd(){
        NotificationCenter.default.addObserver(self, selector: #selector(self.requestAddDestination(sender:)), name: NSNotification.Name(rawValue: Utils.tripDestinationAdded), object: nil)
    }
    
    func requestAddDestination(sender:NSNotification){
        
        var viewController = Application.window != nil ? (Application.window!.rootViewController != nil ? (Application.window!.rootViewController!) : nil) : nil
        
        if(viewController != nil){
            viewController = GeneralFunctions.getVisibleViewController(viewController)
        }
        let dialog = MTDialog()
        let generalFunc = GeneralFunctions()
        Utils.resetAppNotifications()
        
        Utils.closeKeyboard(uv: self)
        
        let userInfo = sender.userInfo
        let jsonDataMsg = userInfo!["body"] as! String
        
        let result = jsonDataMsg.getJsonDataDict()
        
        dialog.build(containerViewController: viewController, title: "", message: result.get("vTitle") == "" ? ((GeneralFunctions()).getLanguageLabel(origValue: "", key: "LBL_DEST_ADD_BY_PASSENGER")) : result.get("vTitle"), positiveBtnTitle: generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT"), negativeBtnTitle: "", action: { (btnClickedIndex) in
            
            let window = Application.window!
            
            let getUserData = GetUserData(uv: viewController == nil ? self : viewController!, window: window)
            getUserData.getdata()
            
        })
        
        dialog.show()
    }
    
    func requestCancelTrip(sender:NSNotification){
        
        let dialog = MTDialog()
        let generalFunc = GeneralFunctions()
        Utils.resetAppNotifications()
        
        Utils.closeKeyboard(uv: self)
        
        let userInfo = sender.userInfo
        let jsonDataMsg = userInfo!["body"] as! String
        
        let result = jsonDataMsg.getJsonDataDict()
        
//        LocalNotification.dispatchlocalNotification(with: "", body: result.get("vTitle"), at: Date().addedBy(seconds: 0), onlyInBackground: true)
        
        var viewController = Application.window != nil ? (Application.window!.rootViewController != nil ? (Application.window!.rootViewController!) : nil) : nil
        
        if(viewController != nil){
            viewController = GeneralFunctions.getVisibleViewController(viewController)
        }
        
        
        dialog.build(containerViewController: viewController, title: "", message: result.get("vTitle") == "" ? ((GeneralFunctions()).getLanguageLabel(origValue: "", key: "LBL_PASSENGER_CANCEL_TRIP_TXT")) : result.get("vTitle"), positiveBtnTitle: generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT"), negativeBtnTitle: "", action: { (btnClickedIndex) in
            
            let window = Application.window!
            
            let getUserData = GetUserData(uv: viewController == nil ? self : viewController!, window: window)
            getUserData.getdata()
            
        })
        
        dialog.show()
    }
    
    func closeDrawerMenu(){
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if(Configurations.isRTLMode()){
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: false)
            self.navigationDrawerController?.closeRightView()
            
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: true)
        }else{
            self.navigationDrawerController?.closeLeftView()
        }
    }
    
    //    func releaseOwnTask(gMapView:GMSMapView?, configPubNub:ConfigPubNub?, uv:UIViewController, getLoc:GetLocation?){
    //
    //        if(gMapView != nil){
    //            gMapView!.stopRendering()
    //            gMapView!.removeFromSuperview()
    //            gMapView!.clear()
    //            gMapView!.delegate = nil
    //        }
    //
    //        if(configPubNub != nil){
    //            configPubNub!.releasePubNub()
    //        }
    //
    //        if(getLoc != nil){
    //            getLoc!.releaseLocationTask()
    //        }
    //        
    //        GeneralFunctions.removeObserver(obj: uv)
    //    }
}
