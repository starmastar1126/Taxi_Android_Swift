//
//  ConfigPubNub.swift
//  DriverApp
//
//  Created by NEW MAC on 25/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
import UserNotifications

class ConfigPubNub: NSObject, PNObjectEventListener, OnLocationUpdateDelegate, OnTaskRunCalledDelegate{
    
    static let removeInst_key = "REMOVE_PUBNUB_INST"
    
    var client: PubNub!
    
    var isRetryKilled = false
    
    var getLocation:GetLocation!
    
    let generalFunc = GeneralFunctions()
    
    var latitude = 0.0
    var longitude = 0.0
    
    var iTripId = ""
    var isSubsToCabReq = false
    
    var isKilled = false
    
    var checkTripStatus:ExeServerUrl!
    
    var updateTripStatusFreqTask:UpdateFreqTask!
    
    var FETCH_TRIP_STATUS_TIME_INTERVAL_INT = 15
    
    func buildPubNub(){
        let configuration = PNConfiguration(publishKey: GeneralFunctions.getValue(key: Utils.PUBNUB_PUB_KEY) as! String, subscribeKey: GeneralFunctions.getValue(key: Utils.PUBNUB_SUB_KEY) as! String)
        
        configuration.uuid = GeneralFunctions.getValue(key: Utils.DEVICE_SESSION_ID_KEY) == nil ? (UIDevice.current.identifierForVendor != nil ? UIDevice.current.identifierForVendor!.uuidString : GeneralFunctions.getMemberd()) : (GeneralFunctions.getValue(key: Utils.DEVICE_SESSION_ID_KEY) as! String)
        configuration.stripMobilePayload = false
        self.client = PubNub.clientWithConfiguration(configuration)
        self.client.addListener(self)
        
        
        self.client.logger.enabled = false
        
        subscribeToPrivateChannel()
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releasePubNub), name: NSNotification.Name(rawValue: ConfigPubNub.removeInst_key), object: nil)
        
        getLocation = GetLocation(uv: nil, isContinuous: true)
        getLocation.buildLocManager(locationUpdateDelegate: self)
        
        
        let FETCH_TRIP_STATUS_TIME_INTERVAL = GeneralFunctions.getValue(key: Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY) != nil ? (GeneralFunctions.getValue(key: Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY) as! String) : "15"
        
        FETCH_TRIP_STATUS_TIME_INTERVAL_INT = GeneralFunctions.parseInt(origValue: 15, data: FETCH_TRIP_STATUS_TIME_INTERVAL)
        
        updateTripStatusFreqTask = UpdateFreqTask(interval: CGFloat(GeneralFunctions.parseFloat(origValue: 15, data: FETCH_TRIP_STATUS_TIME_INTERVAL)))
        updateTripStatusFreqTask.onTaskRunCalled = self
        updateTripStatusFreqTask.currInst = updateTripStatusFreqTask
        updateTripStatusFreqTask.startRepeatingTask()
    }
    
    func onTaskRun(currInst: UpdateFreqTask) {
        if(updateTripStatusFreqTask != nil && currInst == updateTripStatusFreqTask){
            getUserTripStatus()
        }
        scheduleAppInactiveNotifition()
        
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
    }
    
    func scheduleAppInactiveNotifition(){
        
        Utils.removeAppInactiveStateNotifications()
        
        if(self.iTripId == ""){
            if let IS_DRIVER_ONLINE = GeneralFunctions.getValue(key: "IS_DRIVER_ONLINE") as? String{
                if(IS_DRIVER_ONLINE == "true"){
                    Utils.addAppInactiveStateNotification(seconds: FETCH_TRIP_STATUS_TIME_INTERVAL_INT + 5)
                }
            }
        }else{
            Utils.addAppInactiveStateNotification(seconds: FETCH_TRIP_STATUS_TIME_INTERVAL_INT + 5)
        }
    }
    
    func onLocationUpdate(location: CLLocation) {
        latitude = location.coordinate.latitude
        longitude = location.coordinate.longitude
    }
    
    
    func getUserTripStatus(){
        var parameters = ["type": "configDriverTripStatus", "iTripId": self.iTripId, "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "isSubsToCabReq": "\(isSubsToCabReq)"]
        if(latitude != 0.0 && longitude != 0.0){
            parameters["vLatitude"] = "\(latitude)"
            parameters["vLongitude"] = "\(longitude)"
        }
        
        if(checkTripStatus != nil){
            checkTripStatus.cancel()
            
            checkTripStatus = nil
        }
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: UIView(), isOpenLoader: false)
        checkTripStatus = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get(Utils.message_str) == "SESSION_OUT"){
                    Utils.printLog(msgData: "SESSION_OUT_CALLED")
                    if(GeneralFunctions.isAlertViewPresentOnScreenWindow(viewTag: Utils.SESSION_OUT_VIEW_TAG, coverViewTag: Utils.SESSION_OUT_COVER_VIEW_TAG) == false){
                        
                        self.generalFunc.setAlertMessage(uv: nil , title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SESSION_TIME_OUT"), content: self.generalFunc.getLanguageLabel(origValue: "Your session is expired. Please login again.", key: "LBL_SESSION_TIME_OUT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", viewTag: Utils.SESSION_OUT_VIEW_TAG, coverViewTag: Utils.SESSION_OUT_COVER_VIEW_TAG, completionHandler: { (btnClickedIndex) in
                            
                            GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                            GeneralFunctions.postNotificationSignal(key: ConfigPubNub.removeInst_key, obj: self)
                            
                            GeneralFunctions.logOutUser()
                            GeneralFunctions.restartApp(window: Application.window!)
                        })
                    }
                    
                    return
                }
                
                DispatchQueue.main.async {
                    if(dataDict.get("Action") == "1"){
                        
                        if(self.isKilled == false){
                            if(self.iTripId != ""){
                                //                            self.dispatchMsg(result: dataDict.get(Utils.message_str).getJsonDataDict())
                                
                                self.dispatchMsg(result: dataDict.get(Utils.message_str).getJsonDataDict())
                                
                            }else{
                                let msgArr = dataDict.getArrObj(Utils.message_str)
                                
                                for i in 0..<msgArr.count {
                                    let msgStr = (msgArr[i] as! String)                                    
                                    
                                    let dict_temp = msgStr.getJsonDataDict()

                                    self.dispatchMsg(result: dict_temp)
                                }
                            }
                            
                        }
                        
                    }
                }
                
            }
        })
    }
    
    func releasePubNub(){
        isKilled = true
        unSubscribeToPrivateChannel()
        self.client.unsubscribeFromAll()
        self.client.removeListener(self)
        GeneralFunctions.removeObserver(obj: self)
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        if(updateTripStatusFreqTask != nil){
            updateTripStatusFreqTask.stopRepeatingTask()
            updateTripStatusFreqTask = nil
        }
    }
    
    func subscribeToPrivateChannel() {
        self.client.subscribeToChannels(["DRIVER_\(GeneralFunctions.getMemberd())"], withPresence: true)
    }
    
    func unSubscribeToPrivateChannel() {
        self.client.unsubscribeFromChannels(["DRIVERS_\(GeneralFunctions.getMemberd())"], withPresence: true)
    }
    
    func subscribeToCabReqChannel(){
        isSubsToCabReq = true
        self.client.subscribeToChannels(["CAB_REQUEST_DRIVER_\(GeneralFunctions.getMemberd())"], withPresence: true)
    }
    
    func unSubscribeToCabReqChannel(){
        isSubsToCabReq = false
        self.client.unsubscribeFromChannels(["CAB_REQUEST_DRIVER_\(GeneralFunctions.getMemberd())"], withPresence: true)
        
    }
    
    
    func publishMsg(channelName:String, content:String){
        self.client.publish(content, toChannel: channelName,
                            compressed: false, withCompletion: { (status) in
                                
                                if !status.isError {
                                    
                                    // Message successfully published to specified channel.
                                    //                                    print("Message is published:\(channelName)")
                                }
                                else{
                                    
                                    /**
                                     Handle message publish error. Check 'category' property to find
                                     out possible reason because of which request did fail.
                                     Review 'errorData' property (which has PNErrorData data type) of status
                                     object to get additional information about issue.
                                     
                                     Request can be resent using: status.retry()
                                     */
                                    
                                    //                                    print("Error in published:\(status.errorData)::\(channelName)")
                                    //                                    print("Error in published:\(status)")
                                }
        })
    }
    
    
    func client(_ client: PubNub, didReceiveMessage message: PNMessageResult) {
        
        let msg = message.data.message! as! String
        _ = msg.replacingOccurrences(of: "\"", with: "", options: NSString.CompareOptions.literal, range: nil)
        
        let result = msg.getJsonDataDict()
        
        //        if(result != nil){
        //            let dict_temp = result as! NSDictionary
        
        //        }
        
        if(result.get("tSessionId") != "" && result.get("tSessionId") != GeneralFunctions.getSessionId()){
            return
        }
        
        dispatchMsg(result: result)
    }
    
    private func dispatchMsg(result:NSDictionary){
        
        if(self.isKilled == true){
            return
        }
        
        let isMsgExist = GeneralFunctions.isTripStatusMsgExist(msgDataDict: result)
        
        if(isMsgExist == true){
            return
        }
        
        let msg_str = result.get("Message")
        if(msg_str != "" && msg_str == "TripCancelled"){
            NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.tripRequestCanceled), object: self, userInfo: ["body":result.convertToJson()])
        }else if(msg_str != "" && msg_str == "DestinationAdded"){
            
            NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.tripDestinationAdded), object: self, userInfo: ["body":result.convertToJson()])
        }else if(msg_str != ""){
            let msgCode = result.get("MsgCode")
            
            if(msgCode != ""){
                let codeValue = GeneralFunctions.getValue(key: Utils.DRIVER_REQ_CODE_PREFIX_KEY + msgCode)
                
                if(codeValue == nil){
                    NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.passengerRequestArrived), object: self, userInfo: ["body":result.convertToJson()])
                }
            }
            
        }
    }
    
    func client(_ client: PubNub, didReceive status: PNStatus) {
        
        if status.operation == .subscribeOperation {
            
            // Check whether received information about successful subscription or restore.
            if status.category == .PNConnectedCategory || status.category == .PNReconnectedCategory {
                
                let subscribeStatus: PNSubscribeStatus = status as! PNSubscribeStatus
                if subscribeStatus.category == .PNConnectedCategory {
                    
                }
                else {
                    
                    /**
                     This usually occurs if subscribe temporarily fails but reconnects. This means there was
                     an error but there is no longer any issue.
                     */
                }
                
                //                print("PubNub connected")
            }
            else if status.category == .PNUnexpectedDisconnectCategory {
                
                /**
                 This is usually an issue with the internet connection, this is an error, handle
                 appropriately retry will be called automatically.
                 */
                
                isRetryKilled = false
                
                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                    status.retry()
                })
                
                //                print("PubNub disconnected")
            }
                // Looks like some kind of issues happened while client tried to subscribe or disconnected from
                // network.
            else {
                
                let errorStatus: PNErrorStatus = status as! PNErrorStatus
                if errorStatus.category == .PNAccessDeniedCategory {
                    
                    /**
                     This means that PAM does allow this client to subscribe to this channel and channel group
                     configuration. This is another explicit error.
                     */
                }
                else {
                    
                    /**
                     More errors can be directly specified by creating explicit cases for other error categories
                     of `PNStatusCategory` such as: `PNDecryptionErrorCategory`,
                     `PNMalformedFilterExpressionCategory`, `PNMalformedResponseCategory`, `PNTimeoutCategory`
                     or `PNNetworkIssuesCategory`
                     */
                    isRetryKilled = false
                }
            }
        }
    }
    
    
}
