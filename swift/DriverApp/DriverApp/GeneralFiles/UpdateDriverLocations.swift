//
//  UpdateDriverLocations.swift
//  DriverApp
//
//  Created by NEW MAC on 27/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

class UpdateDriverLocations: NSObject, OnLocationUpdateDelegate {
    
    var userLocation:CLLocation!
    var timer:Timer!
    var uv:UIViewController!
    
    var getLoc:GetLocation!
    
    init(uv:UIViewController) {
        self.uv = uv
        super.init()
    }
    
    func scheduleDriverLocUpdate(){
        if(timer != nil){
            timer!.invalidate()
        }
        let DRIVER_LOC_UPDATE_TIME_INTERVAL = GeneralFunctions.getValue(key: "DRIVER_LOC_UPDATE_TIME_INTERVAL")
        let DRIVER_LOC_UPDATE_TIME_INTERVAL_value = GeneralFunctions.parseDouble(origValue: 8.0, data: DRIVER_LOC_UPDATE_TIME_INTERVAL == nil ? "8" : (DRIVER_LOC_UPDATE_TIME_INTERVAL as! String))
        timer =  Timer.scheduledTimer(timeInterval: DRIVER_LOC_UPDATE_TIME_INTERVAL_value, target: self, selector: #selector(updateDriverLocation), userInfo: nil, repeats: true)
        
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
            getLoc.locationUpdateDelegate = nil
        }
    }
    
    func releaseTask(){
        if(timer != nil){
            timer!.invalidate()
            timer = nil
        }
        
        if(getLoc != nil){
            getLoc.releaseLocationTask()
            getLoc.locationUpdateDelegate = nil
            getLoc = nil
        }
    }
    
    func onLocationUpdate(location: CLLocation) {
//        if(self.userLocation != nil){
//            let distance = location.distance(from: self.userLocation)
//            
//            if(distance > 80){
//                self.userLocation = location
//                updateDriverStatus()
//            }
//        }
        if(self.userLocation == nil){
            self.userLocation = location
            updateDriverLocation()
        }
        self.userLocation = location
    }
    
    func updateDriverLocation(){
        
        if(userLocation == nil){
            return
        }
        
        let parameters = ["type":"updateDriverLocations", "iDriverId": GeneralFunctions.getMemberd(),"latitude": "\(userLocation.coordinate.latitude)", "longitude": "\(userLocation.coordinate.longitude)"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
//            print("DriverLocationResponseResponse:\(response)")
            
            
        })
    }

}
