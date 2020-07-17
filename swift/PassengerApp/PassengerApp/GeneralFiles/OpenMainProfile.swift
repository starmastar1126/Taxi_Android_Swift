//
//  OpenMainProfile.swift
//  PassengerApp
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
        
        Configurations.setAppLocal()
        
        
        GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: userProfileJson as AnyObject)
        
        let userProfileJsonDict = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        saveData()
        
        let mainScreenUv = GeneralFunctions.instantiateViewController(pageName: "HomeScreenContainerUV") as! HomeScreenContainerUV
        
        GeneralFunctions.removeAlertViewFromWindow(viewTag: Utils.SESSION_OUT_VIEW_TAG, coverViewTag: Utils.SESSION_OUT_COVER_VIEW_TAG)
        GeneralFunctions.removeAlertViewFromWindow()
        GeneralFunctions.removeAllAlertViewFromNavBar(uv: self.viewControlller)
        
//        if(window.rootViewController != nil && window.rootViewController!.navigationController != nil){
//            window.rootViewController!.navigationController?.popToRootViewController(animated: false)
//            window.rootViewController!.navigationController?.dismiss(animated: false, completion: nil)
//        }else if(window.rootViewController != nil){
//            window.rootViewController?.dismiss(animated: false, completion: nil)
//        }
        
//        let userData = NSKeyedArchiver.archivedData(withRootObject: GeneralFunctions.removeNullsFromDictionary(origin: userProfileJson as! [String : AnyObject]) )
        
        
        if(userProfileJsonDict.get("vEmail") == "" || userProfileJsonDict.get("vPhone") == ""){
            let accountInfoUV = GeneralFunctions.instantiateViewController(pageName: "AccountInfoUV") as! AccountInfoUV
            let navigationController = UINavigationController(rootViewController: accountInfoUV)
            navigationController.navigationBar.isTranslucent = false
            
            GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)

            return
        }
        
        let vTripStatus = userProfileJsonDict.get("vTripStatus")
        
        var Ratings_From_Passenger_str = ""
        var PaymentStatus_From_Passenger_str = ""
        var vTripPaymentMode_str = ""
        
        if(vTripStatus == "Not Active"){
            let TripDetails = userProfileJsonDict.getObj("TripDetails")
            Ratings_From_Passenger_str = userProfileJsonDict.get("Ratings_From_Passenger")
            PaymentStatus_From_Passenger_str = userProfileJsonDict.get("PaymentStatus_From_Passenger_str")
            vTripPaymentMode_str = TripDetails.get("vTripPaymentMode")
            
            vTripPaymentMode_str = "Cash"
            PaymentStatus_From_Passenger_str = "Approved"
            
            if(TripDetails.get("eType") == Utils.cabGeneralType_UberX){
                Ratings_From_Passenger_str = "Done"
            }
        }
        
        
        
        if (vTripStatus != "Not Active" || ((PaymentStatus_From_Passenger_str == "Approved"
            || vTripPaymentMode_str == "Cash") && Ratings_From_Passenger_str == "Done"
            /*&& eVerified_str.equals("Verified")*/)) {
           
            let menuUV = GeneralFunctions.instantiateViewController(pageName: "MenuScreenUV") as! MenuScreenUV
            
            let navigationController = UINavigationController(rootViewController: mainScreenUv)
            navigationController.navigationBar.isTranslucent = false
            if(Configurations.isRTLMode()){
                let navController = NavigationDrawerController(rootViewController: navigationController, leftViewController: nil, rightViewController: menuUV)
                navController.isRightPanGestureEnabled = false
                UIView.transition(with: self.window,
                                  duration: 0.25,
                                  options: .showHideTransitionViews,
                                  animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navController)
                                    
                } ,
                                  completion: nil)
                
            }else{
                let navController = NavigationDrawerController(rootViewController: navigationController, leftViewController: menuUV, rightViewController: nil)
                navController.isLeftPanGestureEnabled = false
                
                UIView.transition(with: self.window,
                                  duration: 0.25,
                                  options: .showHideTransitionViews,
                                  animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navController)
                                    
                } ,
                                  completion: nil)
                
            }
            
        }else{
            let ratingUV = GeneralFunctions.instantiateViewController(pageName: "RatingUV") as! RatingUV
            let navigationController = UINavigationController(rootViewController: ratingUV)
            navigationController.navigationBar.isTranslucent = false
            
            UIView.transition(with: self.window,
                              duration: 0.25,
                              options: .showHideTransitionViews,
                              animations: {GeneralFunctions.changeRootViewController(window: self.window, viewController: navigationController)
                                
            } ,
                              completion: nil)
            
        }
        
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
        GeneralFunctions.saveValue(key: Utils.DEVICE_SESSION_ID_KEY, value: userProfileJson.get("tDeviceSessionId") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.SESSION_ID_KEY, value: userProfileJson.get("tSessionId") as AnyObject)
        GeneralFunctions.saveValue(key: "DESTINATION_UPDATE_TIME_INTERVAL", value: userProfileJson.get("DESTINATION_UPDATE_TIME_INTERVAL") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY, value: userProfileJson.get("FETCH_TRIP_STATUS_TIME_INTERVAL") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.RIDER_REQUEST_ACCEPT_TIME_KEY, value: userProfileJson.get("RIDER_REQUEST_ACCEPT_TIME") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.APP_GCM_SENDER_ID_KEY, value: userProfileJson.get("GOOGLE_SENDER_ID") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.PUBNUB_DISABLED_KEY, value: userProfileJson.get("PUBNUB_DISABLED") as AnyObject)
        
        GeneralFunctions.saveValue(key: Utils.DEFAULT_COUNTRY_KEY, value: userProfileJson.get("vDefaultCountry") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY, value: userProfileJson.get("vDefaultCountryCode") as AnyObject)
        GeneralFunctions.saveValue(key: Utils.DEFAULT_PHONE_CODE_KEY, value: userProfileJson.get("vDefaultPhoneCode") as AnyObject)
        
        
        let userFavouriteAddressArr = userProfileJson.getArrObj("UserFavouriteAddress")
        if(userFavouriteAddressArr.count > 0){
            for i in 0..<userFavouriteAddressArr.count {
                let dataItem = userFavouriteAddressArr[i] as! NSDictionary
                if(dataItem.get("eType").uppercased() == "HOME"){
                    GeneralFunctions.saveValue(key: "userHomeLocationAddress", value: dataItem.get("vAddress") as AnyObject)
                    GeneralFunctions.saveValue(key: "userHomeLocationLatitude", value: dataItem.get("vLatitude") as AnyObject)
                    GeneralFunctions.saveValue(key: "userHomeLocationLongitude", value: dataItem.get("vLongitude") as AnyObject)
                }else if(dataItem.get("eType").uppercased() == "WORK"){
                    GeneralFunctions.saveValue(key: "userWorkLocationAddress", value: dataItem.get("vAddress") as AnyObject)
                    GeneralFunctions.saveValue(key: "userWorkLocationLatitude", value: dataItem.get("vLatitude") as AnyObject)
                    GeneralFunctions.saveValue(key: "userWorkLocationLongitude", value: dataItem.get("vLongitude") as AnyObject)
                }
            }
        }
        
    }
}
