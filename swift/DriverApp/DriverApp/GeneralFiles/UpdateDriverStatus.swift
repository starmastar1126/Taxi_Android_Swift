//
//  UpdateDriverStatus.swift
//  DriverApp
//
//  Created by NEW MAC on 25/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

class UpdateDriverStatus: NSObject, OnLocationUpdateDelegate {

    var userLocation:CLLocation!
    var timer:Timer!
    var uv:UIViewController!
    
    var getLoc:GetLocation!
    
    var isOnline = true
    
    init(uv:UIViewController) {
        self.uv = uv
        super.init()
    }
    
    func scheduleDriverUpdate(){
        if(timer != nil){
            timer!.invalidate()
        }
        timer =  Timer.scheduledTimer(timeInterval: 2 * 60, target: self, selector: #selector(updateDriverStatus), userInfo: nil, repeats: true)
        
        timer.fire()
        
        if(getLoc == nil){
            getLoc = GetLocation(uv: self.uv, isContinuous: true)
            getLoc.buildLocManager(locationUpdateDelegate: self)
        }else{
            getLoc.buildLocManager(locationUpdateDelegate: self)
            getLoc.resumeLocationUpdates()
        }
        
    }
    
    func stopFrequentUpdate(){
        if(timer != nil){
            timer!.invalidate()
        }
        
        if(getLoc != nil){
            getLoc.releaseLocationTask()
        }
    }
    
    func onLocationUpdate(location: CLLocation) {
        if(self.userLocation != nil){
            let distance = location.distance(from: self.userLocation)
            
            if(distance > 80){
                self.userLocation = location
                updateDriverStatus()
            }
        }
        
        self.userLocation = location
    }
    
    func updateDriverStatus(){
        
        if(userLocation == nil){
            return
        }
        
        let parameters = ["type":"updateDriverStatus", "isUpdateOnlineDate": "true", "iDriverId": GeneralFunctions.getMemberd(),"latitude": "\(userLocation.coordinate.latitude)", "longitude": "\(userLocation.coordinate.longitude)", "Status": "\(self.isOnline == true ? "" : "Not Available")"]
//        Available
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
//            print("Response:\(response)")
            
            
        })
    }
}
