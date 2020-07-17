//
//  FireTripStatusMessges.swift
//  PassengerApp
//
//  Created by Tarwinder Singh on 18/12/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class FireTripStatusMessges: NSObject {
    
    let generalFunc = GeneralFunctions()
    
    var mainScreenUv:MainScreenUV!
    var myOnGoingTripDetailsUv:MyOnGoingTripDetailsUV!
    
    var iDriverId = ""
    
    init(mainScreenUv:MainScreenUV? , iDriverId:String){
        self.mainScreenUv = mainScreenUv
        self.iDriverId = iDriverId
    }
    

    func fireTripMsg(result:NSDictionary){
        let isMsgExist = GeneralFunctions.isTripStatusMsgExist(msgDataDict: result)
        
        Utils.printLog(msgData: "isMsgExist:\(isMsgExist)")
        if(isMsgExist == true){
            return
        }
        
        var viewController = Application.window != nil ? (Application.window!.rootViewController != nil ? (Application.window!.rootViewController!) : nil) : nil
        
        if(viewController != nil){
            viewController = GeneralFunctions.getVisibleViewController(viewController)
        }
        
        if(viewController?.navigationController != nil && viewController?.navigationController!.viewControllers.count == 1){
            viewController = nil
        }
        
        let msg_str = result.get("Message")
        let msg_pub_str = result.get("MsgType")
        
        let eType = result.get("eType")
        var contentMsg = result.get("vTitle")
        let driverName = result.get("driverName")
        let vRideNo = result.get("vRideNo")
        let iTripId = result.get("iTripId")
        
        if(msg_pub_str == "LocationUpdate"){
            let iDriverId = result.get("iDriverId")
            let vLatitude = result.get("vLatitude")
            let vLongitude = result.get("vLongitude")
            
            DispatchQueue.main.async {
                self.mainScreenUv?.updateDriverLocationBeforeTrip(iDriverId: iDriverId, latitude: vLatitude, longitude: vLongitude, dataDict: result)
            }
            
        }else if(msg_pub_str == "TripRequestCancel"){
            self.mainScreenUv?.incCountOfRequestToDriver()
            
        }else if(msg_pub_str == "LocationUpdateOnTrip"){
            let iDriverId = result.get("iDriverId")
            let vLatitude = result.get("vLatitude")
            let vLongitude = result.get("vLongitude")
            
            if(self.mainScreenUv != nil){
                DispatchQueue.main.async {
                    self.mainScreenUv?.updateDriverLocation(iDriverId: iDriverId, latitude: vLatitude, longitude: vLongitude, dataDict: result)
                }
            }else if(self.myOnGoingTripDetailsUv != nil){
                if(self.iDriverId == iDriverId){
                    DispatchQueue.main.async {
                        self.myOnGoingTripDetailsUv?.updateDriverLocation(iDriverId: iDriverId, latitude: vLatitude, longitude: vLongitude)
                    }
                }
            }
            
        }else if(msg_pub_str == "DriverArrived"){
            
            if(eType == Utils.cabGeneralType_UberX){
                if(self.myOnGoingTripDetailsUv != nil){
                    self.generalFunc.setAlertMessage(uv: self.myOnGoingTripDetailsUv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVE"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                    })
                }else{
                    if(contentMsg == ""){
                        contentMsg = "\(generalFunc.getLanguageLabel(origValue: "", key: "LBL_DELIVERY_DRIVER_TXT")) \(driverName)  \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVED_NOTIMSG")) \(vRideNo)"
                    }
//                    (self.mainScreenUv == nil ? (self.myOnGoingTripDetailsUv == nil ? self.ufxHomeScreenUv : self.myOnGoingTripDetailsUv) : self.mainScreenUv)
                    self.generalFunc.setAlertMessage(uv: viewController, title: "", content: contentMsg, positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                    })
                }
            }else{
                if(contentMsg == ""){
                    if(eType == Utils.cabGeneralType_Ride){
                        contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVED_TXT")
                    }else{
                        contentMsg = "\(generalFunc.getLanguageLabel(origValue: "", key: "LBL_DELIVERY_DRIVER_TXT")) \(driverName)  \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVED_NOTIMSG")) \(vRideNo)"
                    }
                }
//                (self.mainScreenUv == nil ? (self.myOnGoingTripDetailsUv == nil ? self.ufxHomeScreenUv : self.myOnGoingTripDetailsUv) : self.mainScreenUv)
                self.generalFunc.setAlertMessage(uv: viewController, title: "", content: contentMsg, positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                })
            }
            
            if(self.mainScreenUv != nil && eType != Utils.cabGeneralType_UberX){
                DispatchQueue.main.async {
                    self.mainScreenUv?.setDriverArrivedStatus()
                }
            }else if(self.myOnGoingTripDetailsUv != nil){
                let iDriverId = result.get("iDriverId")
                if(self.iDriverId == iDriverId){
                    DispatchQueue.main.async {
                        self.myOnGoingTripDetailsUv?.setDriverArrivedStatus()
                    }
                }
            }
            
        }else if(msg_str != ""){
            
            if(msg_str == "TripStarted"){
                if(contentMsg == ""){
                    if(eType == Utils.cabGeneralType_Ride){
                        contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_START_TRIP_DIALOG_TXT")
                    }else{
                        contentMsg = "\(generalFunc.getLanguageLabel(origValue: "", key: "LBL_DELIVERY_DRIVER_TXT")) \(driverName)  \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_START_NOTIMSG")) \(vRideNo)"
                    }
                }
//               (self.mainScreenUv == nil ? (self.myOnGoingTripDetailsUv == nil ? self.ufxHomeScreenUv : self.myOnGoingTripDetailsUv) : self.mainScreenUv)
                self.generalFunc.setError(uv: viewController, title: "", content:  contentMsg)
                
            }else if(msg_str == "TripCancelledByDriver" || msg_str == "TripEnd"){
                if(contentMsg == ""){
                    
                    if(msg_str == "TripCancelledByDriver"){
                        contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PREFIX_TRIP_CANCEL_DRIVER") + " " + result.get("Reason") + " " + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TRIP_BY_DRIVER_MSG_SUFFIX")
                    }else{
                        if(eType == Utils.cabGeneralType_Ride){
                            contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_END_TRIP_DIALOG_TXT")
                        }else{
                            contentMsg = "\(driverName) \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_END_NOTIMSG")) \(vRideNo)"
                        }
                    }
                }
                
                if(eType != Utils.cabGeneralType_UberX){
                    GeneralFunctions.postNotificationSignal(key: ConfigPubNub.removeInst_key, obj: self)
                    GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                }else{
                    GeneralFunctions.saveValue(key: Utils.IS_WALLET_AMOUNT_UPDATE_KEY, value: "true" as AnyObject)
                }
                
                NotificationCenter.default.post(name: NSNotification.Name(rawValue: ConfigPubNub.TRIP_COMPLETE_NOTI_OBSERVER_KEY), object: self, userInfo: ["body":String(describing: result.convertToJson())])
                
                
                
                self.generalFunc.setAlertMessage(uv: viewController, title: "", content: contentMsg, positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    if(eType != Utils.cabGeneralType_UberX){
                        let window = Application.window
//                        (self.mainScreenUv == nil ? (self.myOnGoingTripDetailsUv == nil ? self.ufxHomeScreenUv : self.myOnGoingTripDetailsUv) : self.mainScreenUv)
                        let getUserData = GetUserData(uv: viewController, window: window!)
                        getUserData.getdata()
                    }else{
                        if(viewController != nil && (msg_str == "TripEnd" || msg_str == "TripCancelledByDriver" && result.get("ShowTripFare").uppercased() == "TRUE")){
                            let ratingUV = GeneralFunctions.instantiateViewController(pageName: "RatingUV") as! RatingUV
                            ratingUV.iTripId = iTripId
                            viewController!.pushToNavController(uv: ratingUV, isDirect: true)
                        }
                    }
                })
                return
            }
            NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.driverCallBackNotificationKey), object: self, userInfo: ["body":String(describing: result.convertToJson())])
        }
    }
}
