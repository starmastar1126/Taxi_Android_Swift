//
//  UpdateTripLocationService.swift
//  DriverApp
//
//  Created by NEW MAC on 14/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

class UpdateTripLocationService: NSObject, OnLocationUpdateDelegate {
    
    var userLocation:CLLocation!
    var timer:Timer!
    var uv:UIViewController!
    
    var getLoc:GetLocation!
    
    let UPDATE_TIME_INTERVAL:Double = 1 * 60
    
    var latitudeList = [String]()
    var longitudeList = [String]()
    
    var tripId = ""
    
    var currentExeWebServerUrl:ExeServerUrl!
    
    init(uv:UIViewController) {
        self.uv = uv
        super.init()
    }
    
    func scheduleDriverLocUpdate(){
        if(timer != nil){
            timer!.invalidate()
        }
        
        timer =  Timer.scheduledTimer(timeInterval: UPDATE_TIME_INTERVAL, target: self, selector: #selector(updateDriverLocation), userInfo: nil, repeats: true)
        
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
       
//        if(self.userLocation == nil){
//            self.userLocation = location
//            updateDriverLocation()
//        }
        
        self.userLocation = location
        
        self.latitudeList += ["\(userLocation.coordinate.latitude)"]
        self.longitudeList += ["\(userLocation.coordinate.longitude)"]
    }
    
    func updateDriverLocation(){
        
        if(self.latitudeList.count == 0 || self.longitudeList.count == 0){
            self.latitudeList.removeAll()
            self.longitudeList.removeAll()
            return
        }
        var tempListOfLatitude = [String]()
        var tempListOfLongitude = [String]()
        
        tempListOfLatitude.append(contentsOf: self.latitudeList)
        tempListOfLongitude.append(contentsOf: self.longitudeList)
        
        let latitudeList = tempListOfLatitude.joined(separator:",")
        let longitudeList = tempListOfLongitude.joined(separator:",")
        
        
        if(self.currentExeWebServerUrl != nil){
            self.currentExeWebServerUrl.cancel()
            self.currentExeWebServerUrl = nil
        }
        
        let parameters = ["type":"updateTripLocations", "iDriverId": GeneralFunctions.getMemberd(), "TripId": self.tripId,"latList": "\(latitudeList)", "lonList": "\(longitudeList)"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                if(dataDict.get("Action") == "1"){
                    
                    for _ in 0..<tempListOfLatitude.count {
                        self.latitudeList.removeFirst()
                    }
                    
                    for _ in 0..<tempListOfLongitude.count {
                        self.longitudeList.removeFirst()
                    }
                }
            }
            
            
        })
        
        self.currentExeWebServerUrl = exeWebServerUrl
    }

}
