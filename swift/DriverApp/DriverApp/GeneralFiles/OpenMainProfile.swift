//
//  OpenMainProfile.swift
//  DriverApp
//
//  Created by NEW MAC on 11/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenMainProfile: NSObject {
    
    var window :UIWindow!
    var viewControlller:UIViewController!
    var userProfileJson:String!
    let generalFunc = GeneralFunctions()
    
    init(uv: UIViewController, userProfileJson:String, window :UIWindow) {
        self.viewControlller = uv
        self.userProfileJson = userProfileJson
        self.window = window
        super.init()
        
        openProfile()
    }
    
    func openProfile(){
//        changeRootViewController
        
        saveData()
        
        
        GeneralFunctions.removeAllAlertViewFromNavBar(uv: self.viewControlller)
        
        GeneralFunctions.removeAlertViewFromWindow(viewTag: Utils.WINDOW_ALERT_DIALOG_CONTENT_TAG, coverViewTag: Utils.WINDOW_ALERT_DIALOG_BG_TAG)
        
        Configurations.setAppLocal()
        
        let mainScreenUv = GeneralFunctions.instantiateViewController(pageName: "HomeScreenContainerUV") as! HomeScreenContainerUV
        
        
//        if(window.rootViewController != nil && window.rootViewController!.navigationController != nil){
//            window.rootViewController!.navigationController?.popToRootViewController(animated: false)
//            window.rootViewController!.navigationController?.dismiss(animated: false, completion: nil)
//        }else if(window.rootViewController != nil){
//            window.rootViewController?.dismiss(animated: false, completion: nil)
//        }
        
        
//        let windowSubViews = self.window.subviews
//        
//        for i in 0..<windowSubViews.count{
//            let subView = windowSubViews[i]
//            subView.removeFromSuperview()
//        }
        
        
//        let userData = NSKeyedArchiver.archivedData(withRootObject: GeneralFunctions.removeNullsFromDictionary(origin: userProfileJson as! [String : AnyObject]) )
        
        GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: userProfileJson as AnyObject)
        
        let userProfileJsonDict = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        if(userProfileJsonDict.get("vEmail") == "" || userProfileJsonDict.get("vPhone") == ""){
            let accountInfoUV = GeneralFunctions.instantiateViewController(pageName: "AccountInfoUV") as! AccountInfoUV
            let navigationController = UINavigationController(rootViewController: accountInfoUV)
            navigationController.navigationBar.isTranslucent = false
            
            UIView.transition(with: self.window,
                              duration: 0.25,
                              options: .showHideTransitionViews,
                              animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                
            } ,
                              completion: nil)
            

            return
        }
        
        
        let vTripStatus = userProfileJsonDict.get("vTripStatus")
        var lastTripExist = false
        
        if(vTripStatus == "Not Active"){
            let ratings_From_Driver = userProfileJsonDict.get("Ratings_From_Driver")
            
            if(ratings_From_Driver != "Done"){
                lastTripExist = true
            }
        }
        
        if (vTripStatus != "" && vTripStatus != "NONE"  && vTripStatus != "Cancelled"
            && (vTripStatus == "Active" || vTripStatus == "On Going Trip"
                || vTripStatus == "Arrived" || lastTripExist == true)) {
        
            let last_trip_data = userProfileJsonDict.getObj("TripDetails")
            let passenger_data = userProfileJsonDict.getObj("PassengerDetails")
            
         
            
            
             let dataParams = ["Message":"CabRequested", "sourceLatitude": last_trip_data.get("tStartLat"), "sourceLongitude": last_trip_data.get("tStartLong"), "PassengerId": last_trip_data.get("iUserId"), "PName": "\(passenger_data.get("vName"))", "PPicName": "\(passenger_data.get("vImgName"))", "PFId": "\(passenger_data.get("vFbId"))", "PRating": "\(passenger_data.get("vAvgRating"))", "PPhone": "\(passenger_data.get("vPhone"))", "PPhoneC": "\(passenger_data.get("vPhoneCode"))", "PAppVersion": "\(passenger_data.get("iAppVersion"))", "TripId": "\(last_trip_data.get("iTripId"))", "DestLocLatitude": "\(last_trip_data.get("tEndLat"))", "DestLocLongitude": "\(last_trip_data.get("tEndLong"))", "DestLocAddress": "\(last_trip_data.get("tDaddress"))", "REQUEST_TYPE": "\(last_trip_data.get("eType"))", "vDeliveryConfirmCode": "\(last_trip_data.get("vDeliveryConfirmCode"))", "SITE_TYPE": "\(userProfileJsonDict.get("SITE_TYPE"))", "eTollSkipped": "No", "eHailTrip": "\(last_trip_data.get("eHailTrip"))", "eFareType": "\(last_trip_data.get("eFareType"))", "TimeState": "\(userProfileJsonDict.get("TimeState"))", "TotalSeconds": "\(userProfileJsonDict.get("TotalSeconds"))", "iTripTimeId": "\(userProfileJsonDict.get("iTripTimeId"))", "tUserComment": "\(last_trip_data.get("tUserComment"))", "eBeforeUpload": "\(last_trip_data.get("eBeforeUpload"))", "eAfterUpload": "\(last_trip_data.get("eAfterUpload"))", "tSaddress": "\(last_trip_data.get("tSaddress"))", "tDaddress": "\(last_trip_data.get("tDaddress"))", "eIconType" : "\(last_trip_data.get("eIconType"))"]
            
            
            if (vTripStatus == "Not Active" && lastTripExist == true) {
                
                let ePaymentCollect = last_trip_data.get("ePaymentCollect")
                
                if(ePaymentCollect == "No"){
                    // CollectPayment
                    let collectPaymentUv = GeneralFunctions.instantiateViewController(pageName: "CollectPaymentUV") as! CollectPaymentUV
                    collectPaymentUv.tripData = dataParams as NSDictionary
                    let navigationController = UINavigationController(rootViewController: collectPaymentUv)
                    navigationController.navigationBar.isTranslucent = false
                    
                    UIView.transition(with: self.window,
                                      duration: 0.25,
                                      options: .showHideTransitionViews,
                                      animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                        
                    } ,
                                      completion: nil)
                    
                    
                    return
                }else{
                    // Trip Rating
                    let ratingUv = GeneralFunctions.instantiateViewController(pageName: "RatingUV") as! RatingUV
                    ratingUv.tripData = dataParams as NSDictionary
                    let navigationController = UINavigationController(rootViewController: ratingUv)
                    navigationController.navigationBar.isTranslucent = false
                    
                    UIView.transition(with: self.window,
                                      duration: 0.25,
                                      options: .showHideTransitionViews,
                                      animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                        
                    } ,
                                      completion: nil)
                    
                    return
                }
            
            }else{
                
                if (vTripStatus == "Arrived") {
                    // Open active trip page
//                    map.put("vTripStatus", "Arrived");
//                     bn.putSerializable("TRIP_DATA", map);
                    
//                    new StartActProcess(mContext).startActWithData(ActiveTripActivity.class, bn);
                    
                    let activeTripUV = GeneralFunctions.instantiateViewController(pageName: "ActiveTripUV") as! ActiveTripUV
                    activeTripUV.tripData = dataParams as NSDictionary
                    activeTripUV.isTripStarted = false
                    
                    let navigationController = UINavigationController(rootViewController: activeTripUV)
                    navigationController.navigationBar.isTranslucent = false
                    
                    UIView.transition(with: self.window,
                                      duration: 0.25,
                                      options: .showHideTransitionViews,
                                      animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                        
                    } ,
                                      completion: nil)
                    
                    
                    return
                } else if (vTripStatus != "Arrived" && vTripStatus == "On Going Trip") {
                    // Open active trip page
//                    map.put("vTripStatus", "EN_ROUTE");
//                    bn.putSerializable("TRIP_DATA", map);
//                    
//                    new StartActProcess(mContext).startActWithData(ActiveTripActivity.class, bn);
                    
                    let activeTripUV = GeneralFunctions.instantiateViewController(pageName: "ActiveTripUV") as! ActiveTripUV
                    activeTripUV.tripData = dataParams as NSDictionary
                    activeTripUV.isTripStarted = true
                    
                    let navigationController = UINavigationController(rootViewController: activeTripUV)
                    navigationController.navigationBar.isTranslucent = false
                    
                    UIView.transition(with: self.window,
                                      duration: 0.25,
                                      options: .showHideTransitionViews,
                                      animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                        
                    } ,
                                      completion: nil)
                    
                    
                    return
                } else if (vTripStatus != "Arrived" && vTripStatus == "Active") {
                    // Open driver arrived page
                    let driverArrivedUv = GeneralFunctions.instantiateViewController(pageName: "DriverArrivedUV") as! DriverArrivedUV
                    driverArrivedUv.tripData = dataParams as NSDictionary
                    let navigationController = UINavigationController(rootViewController: driverArrivedUv)
                    navigationController.navigationBar.isTranslucent = false
                    
                    UIView.transition(with: self.window,
                                      duration: 0.25,
                                      options: .showHideTransitionViews,
                                      animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                        
                    } ,
                                      completion: nil)
                    
                    
                    return
                }
            
            }
        }
        
        let menuUV = GeneralFunctions.instantiateViewController(pageName: "MenuScreenUV") as! MenuScreenUV
//        menuUV.mainScreenUv = mainScreenUv
        
//        mainScreenUv.menuScreenUv = menuUV
        
        let navigationController = UINavigationController(rootViewController: mainScreenUv)
        navigationController.navigationBar.isTranslucent = false
        if(Configurations.isRTLMode()){
            let navController = NavigationDrawerController(rootViewController: navigationController, leftViewController: nil, rightViewController: menuUV)
            navController.isRightPanGestureEnabled = false
            GeneralFunctions.changeRootViewController(window: self.window, viewController: navController)
        }else{
            let navController = NavigationDrawerController(rootViewController: navigationController, leftViewController: menuUV, rightViewController: nil)
            navController.isLeftPanGestureEnabled = false
            GeneralFunctions.changeRootViewController(window: self.window, viewController: navController)
        }
        
//        if(oldViewController != nil){
//            oldViewController.dismiss(animated: false, completion: nil)
//        }
    }
    
    func saveData(){
        let userProfileJson = self.userProfileJson.getJsonDataDict().getObj(Utils.message_str)
        

        GeneralFunctions.saveValue(key: Utils.ENABLE_PUBNUB_KEY, value: userProfileJson.get("ENABLE_PUBNUB") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.PUBNUB_PUB_KEY, value: userProfileJson.get("PUBNUB_PUBLISH_KEY") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.PUBNUB_SUB_KEY, value: userProfileJson.get("PUBNUB_SUBSCRIBE_KEY") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.PUBNUB_SEC_KEY, value: userProfileJson.get("PUBNUB_SECRET_KEY") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.SITE_TYPE_KEY, value: userProfileJson.get("SITE_TYPE") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY, value: userProfileJson.get("MOBILE_VERIFICATION_ENABLE") as AnyObject)
        GeneralFunctions.saveValue(key: "LOCATION_ACCURACY_METERS", value: userProfileJson.get("LOCATION_ACCURACY_METERS") as AnyObject)
        GeneralFunctions.saveValue(key: "DRIVER_LOC_UPDATE_TIME_INTERVAL", value: userProfileJson.get("DRIVER_LOC_UPDATE_TIME_INTERVAL") as AnyObject)
        GeneralFunctions.saveValue(key: "PHOTO_UPLOAD_SERVICE_ENABLE", value: userProfileJson.get("PHOTO_UPLOAD_SERVICE_ENABLE") as AnyObject)
        GeneralFunctions.saveValue(key: "DESTINATION_UPDATE_TIME_INTERVAL", value: userProfileJson.get("DESTINATION_UPDATE_TIME_INTERVAL") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY, value: userProfileJson.get("FETCH_TRIP_STATUS_TIME_INTERVAL") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.RIDER_REQUEST_ACCEPT_TIME_KEY, value: userProfileJson.get("RIDER_REQUEST_ACCEPT_TIME") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.APP_GCM_SENDER_ID_KEY, value: userProfileJson.get("GOOGLE_SENDER_ID") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.PUBNUB_DISABLED_KEY, value: userProfileJson.get("PUBNUB_DISABLED") as AnyObject)
        
        GeneralFunctions.saveValue(key: Utils.DEVICE_SESSION_ID_KEY, value: userProfileJson.get("tDeviceSessionId") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.SESSION_ID_KEY, value: userProfileJson.get("tSessionId") as AnyObject)
        
        GeneralFunctions.saveValue(key: Utils.DEFAULT_COUNTRY_KEY, value: userProfileJson.get("vDefaultCountry") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY, value: userProfileJson.get("vDefaultCountryCode") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.DEFAULT_PHONE_CODE_KEY, value: userProfileJson.get("vDefaultPhoneCode") as AnyObject)
        
    }
}
