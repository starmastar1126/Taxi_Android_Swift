//
//  AppDelegate.swift
//  DriverApp
//
//  Created by NEW MAC on 04/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps
import AVFoundation
import GooglePlaces
import GooglePlacePicker
import GoogleSignIn
import FirebaseCore
import Firebase
import FirebaseMessaging
import Fabric
import Crashlytics


@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {

    var window: UIWindow?

    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplicationLaunchOptionsKey: Any]?) -> Bool {
        // Override point for customization after application launch.
        Configurations.setAppLocal()
        Fabric.with([Crashlytics.self])

        SDImageCache.shared().clearMemory()
        SDImageCache.shared().clearDisk()
        SDImageCache.shared().cleanDisk()
        
        if launchOptions?[UIApplicationLaunchOptionsKey.remoteNotification] != nil {

            let userInfo = launchOptions?[UIApplicationLaunchOptionsKey.remoteNotification] as! [AnyHashable : Any]
            
            let notification = userInfo["aps"] as? NSDictionary
            
            
            if(notification?.get("body") != "" && (notification!.get("body")).getJsonDataDict().get("MsgType") == "CHAT"){
                
                if(Application.window != nil && Application.window?.rootViewController != nil){
                    
                    if(GeneralFunctions.getVisibleViewController(Application.window!.rootViewController) != nil && GeneralFunctions.getVisibleViewController(Application.window!.rootViewController)!.className != "ChatUV"){
                        GeneralFunctions.saveValue(key: "OPEN_MSG_SCREEN", value: "true" as AnyObject)
                    }
                }
            }
        }
        
        GMSServices.provideAPIKey(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))
        GMSPlacesClient.provideAPIKey(Configurations.getInfoPlistValue(key: "GOOGLE_IOS_APP_KEY"))
        
        Configurations.setAppThemeNavBar()
        
//        UINavigationBar.appearance().titleTextAttributes = [NSFontAttributeName: UIFont(name: "Roboto-Light", size: 24)!]
        
//        UINavigationBar.appearance().shadowImage = UIImage()
//        UINavigationBar.appearance().setBackgroundImage(UIImage(), for: .default)
        
        
        FBSDKApplicationDelegate.sharedInstance().application(application, didFinishLaunchingWithOptions: launchOptions)
        
        IQKeyboardManager.shared().isEnabled = true
        IQKeyboardManager.shared().toolbarDoneBarButtonItemText = (GeneralFunctions()).getLanguageLabel(origValue: "Done", key: "LBL_DONE")
        
        IQKeyboardManager.shared().disabledToolbarClasses.add(ChatUV.self)
        IQKeyboardManager.shared().disabledDistanceHandlingClasses.add(ChatUV.self)
//        Fabric.with([Twitter.self])
//        Fabric.with([STPAPIClient.self, Twitter.self])
        
//        FIRInstanceID.instanceID().delete { (err:Error?) in
//            if err != nil{
//                print(err.debugDescription);
//            } else {
//                print("Token Deleted");
//            }
//        }
        AnalyticsConfiguration.shared().setAnalyticsCollectionEnabled(true)

        FirebaseApp.configure()

        
        
        LocalNotification.registerForLocalNotification(on: UIApplication.shared)
        GeneralFunctions.registerRemoteNotification()
        
         Crashlytics.sharedInstance().setUserIdentifier("\(Utils.appUserType.uppercased())_\(GeneralFunctions.getMemberd())")
        return true
    }
    
    func application(_ application: UIApplication, open url: URL, sourceApplication: String?, annotation: Any) -> Bool {
        return FBSDKApplicationDelegate.sharedInstance().application(application, open: url, sourceApplication: sourceApplication, annotation: annotation)
    }
    
    func application(_ app: UIApplication, open url: URL, options: [UIApplicationOpenURLOptionsKey : Any] = [:]) -> Bool {
        
        let isFacebookURL = url.scheme != nil && url.scheme!.hasPrefix("fb\(FBSDKSettings.appID()!)") && url.host != nil && url.host! == "authorize"
        if isFacebookURL {
            return FBSDKApplicationDelegate.sharedInstance().application(app, open: url, sourceApplication: options[UIApplicationOpenURLOptionsKey.sourceApplication] as? String, annotation: [:])
        }

        let isGoogleUrl = url.scheme != nil && url.scheme!.hasPrefix("com.googleusercontent.apps")
        
        if(isGoogleUrl){
            return GIDSignIn.sharedInstance().handle(url as URL!,
                                                     sourceApplication:options[UIApplicationOpenURLOptionsKey.sourceApplication] as? String,
                                                     annotation: [:])
        }
        
        return Twitter.sharedInstance().application(app, open: url as URL!, options: options)
        
//        return GIDSignIn.sharedInstance().handle(url as URL!,
//                                                 sourceApplication:options[UIApplicationOpenURLOptionsKey.sourceApplication] as? String,
//                                                 annotation: [:])
    }
    

    func applicationWillResignActive(_ application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
        
         FBSDKAppEvents.activateApp()
    }

    func applicationDidEnterBackground(_ application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
        
        GeneralFunctions.postNotificationSignal(key: Utils.appBGNotificationKey, obj: self)
        
        UIApplication.shared.isIdleTimerDisabled = false
        
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
    }

    func applicationWillEnterForeground(_ application: UIApplication) {
        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.

//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
    }

    func applicationDidBecomeActive(_ application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
        
        GeneralFunctions.postNotificationSignal(key: Utils.appFGNotificationKey, obj: self)
        
        UIApplication.shared.isIdleTimerDisabled = true
        
        Utils.resetAppNotifications()
    }

    func applicationWillTerminate(_ application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
        GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
        GeneralFunctions.postNotificationSignal(key: ConfigPubNub.removeInst_key, obj: self)
    }

    func application(_ application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
        
//        let token = String(data: deviceToken.base64EncodedData(), encoding: .utf8)?.trimmingCharacters(in: CharacterSet.whitespaces).trimmingCharacters(in: CharacterSet(charactersIn: "<>"))
        let token = deviceToken.map { String(format: "%02.2hhx", $0) }.joined()
        
//        if(token == nil){
//            return
//        }
        
        
//        let fcmDeviceToken = FIRInstanceID.instanceID().token()
//        print("InstanceIDtoken: \(fcmDeviceToken)")
        
        
        print("device token::" + token)
        GeneralFunctions.saveValue(key: "APNID", value: token as AnyObject)
        
//        if(fcmDeviceToken == nil){
//            
//            FIRInstanceID.instanceID().setAPNSToken(deviceToken, type: .sandbox)
//            FIRInstanceID.instanceID().setAPNSToken(deviceToken, type: .prod)
//            
//            NotificationCenter.default.addObserver(self,
//                                                   selector: #selector(self.tokenRefreshNotification(notification:)),
//                                                   name: .firInstanceIDTokenRefresh,
//                                                   object: nil)
//            
//            return
//        }
        
//        callBackToken(deviceToken: token, fcmToken: fcmDeviceToken!)
//        GeneralFunctions.postNotificationSignal(key: Utils.apnIDNotificationKey, obj: token! as AnyObject)
        
//        NotificationCenter.default.postNotificationName(Utils.apnIDNotificationKey, object: self, userInfo: ["body":token])
        NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.apnIDNotificationKey), object: nil, userInfo: ["body":token])

    }
    
    
    func callBackToken(deviceToken:String, fcmToken:String){
        NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.apnIDNotificationKey), object: nil, userInfo: ["body":deviceToken, "FCMToken": fcmToken])
    }
    
//    func tokenRefreshNotification(notification: NSNotification) {
//        //  print("refresh token call")
//        var fcmDeviceToken = ""
//        
//        if(FIRInstanceID.instanceID().token() != nil){
//            fcmDeviceToken = FIRInstanceID.instanceID().token()!
//        }
//      
//        // let refreshedToken = FIRInstanceID.instanceID().token()!
//        print("InstanceID token: \(fcmDeviceToken)")
//        
//        GeneralFunctions.removeObserver(obj: self)
//        
//        let deviceToken = GeneralFunctions.getValue(key: "APNID") as! String
//        callBackToken(deviceToken: deviceToken, fcmToken: fcmDeviceToken)
//        
//        GeneralFunctions.saveValue(key: "FCMDeviceToken", value: fcmDeviceToken as AnyObject)
//    }
    
    func application(_ application: UIApplication, didFailToRegisterForRemoteNotificationsWithError error: Error) {
        print("ErrorInReg:\(error)")
        
        if(UIDevice().type == .simulator){
            let token = "simulator_demo_1234"
            GeneralFunctions.saveValue(key: "APNID", value: token as AnyObject)
            NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.apnIDNotificationKey), object: nil, userInfo: ["body":token])
        }
    }
    
    func application(_ application: UIApplication, didRegister notificationSettings: UIUserNotificationSettings) {
        
    }
    
    func application(_ application: UIApplication, didReceive notification: UILocalNotification) {
        Utils.resetAppNotifications()
    }
    
    
    func application(_ application: UIApplication, didReceiveRemoteNotification userInfo: [AnyHashable : Any]) {
        
        let notification = userInfo["aps"] as? NSDictionary
        
        if(notification?.get("body") == ""){
            return
        }
        
        Utils.resetAppNotifications()
        
        if(notification?.get("body") != "" && (notification!.get("body")).getJsonDataDict().get("MsgType") == "CHAT"){
            
            //            if(Application.window != nil && Application.window?.rootViewController != nil && application.applicationState == UIApplicationState.active && Utils.isMyAppInBackground() == false && notification?.get("gcm.message_id") != ""){
            //                (GeneralFunctions()).setError(uv: Application.window!.rootViewController!, title: "", content: notification!.get("alert"))
            //            }else
            
            if(Application.window != nil && Application.window?.rootViewController != nil){
                
                if(GeneralFunctions.getVisibleViewController(Application.window!.rootViewController) != nil && GeneralFunctions.getVisibleViewController(Application.window!.rootViewController)!.className != "ChatUV"){
                    
                    let receiverName = (notification!.get("body")).getJsonDataDict().get("FromMemberName")
                    let receiverId = (notification!.get("body")).getJsonDataDict().get("iFromMemberId")
                    let tripId = (notification!.get("body")).getJsonDataDict().get("iTripId")
                    let fromMemberImageName = (notification!.get("body")).getJsonDataDict().get("FromMemberImageName")
                    
                    let chatUv = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
                    chatUv.receiverId = receiverId
                    chatUv.receiverDisplayName = receiverName
                    chatUv.assignedtripId = tripId
                    chatUv.pPicName = fromMemberImageName
                    
                    Application.window!.rootViewController?.pushToNavController(uv: chatUv, isDirect: true)
                    
                    return
                    
                }
            }
            
            return
            
        }
        
        
        let jsonData = notification!["body"] as! String
        let result = jsonData.getJsonDataDict()
        Utils.printLog(msgData: "AppDelegate:\(result)")
        let msg_str = result.get("Message")
        
        let isMsgExist = GeneralFunctions.isTripStatusMsgExist(msgDataDict: result)
        
        if(isMsgExist == true){
            return
        }
        
        if(msg_str != "" && msg_str == "TripCancelled"){
            //            GeneralFunctions.postNotificationSignal(key: Utils.tripRequestCanceled, obj: self, userInfo: ["body":jsonData])
            NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.tripRequestCanceled), object: self, userInfo: ["body": jsonData])
            return
        }else if(msg_str != ""){
            NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.passengerRequestArrived), object: self, userInfo: ["body":jsonData])
        }else if(jsonData.trim() != ""){
            
            if(Application.window != nil && Application.window?.rootViewController != nil && Utils.isMyAppInBackground() == false){
                (GeneralFunctions()).setError(uv: Application.window!.rootViewController!, title: "", content: jsonData)
            }
        }
        
       
    }
}
