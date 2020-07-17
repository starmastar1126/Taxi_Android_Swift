//
//  GetLocation.swift
//  DriverApp
//
//  Created by NEW MAC on 25/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation


extension OnLocationUpdateDelegate {
    func onHeadingUpdate(heading:Double){
        
    }
    
    func onLocationUpdate(location:CLLocation){
        
    }
}

protocol OnLocationUpdateDelegate {
    func onLocationUpdate(location:CLLocation)
    func onHeadingUpdate(heading:Double)
}

class GetLocation: NSObject, CLLocationManagerDelegate {
    
    var uv:UIViewController!
    var isContinuous:Bool!
    
    var locationUpdateDelegate:OnLocationUpdateDelegate?
    
    var locationManager:CLLocationManager!
    
    init(uv:UIViewController?, isContinuous:Bool) {
        self.uv = uv
        self.isContinuous = isContinuous
        
        super.init()
    }
    
    func buildLocManager(locationUpdateDelegate:OnLocationUpdateDelegate){
        
        DispatchQueue.main.async {
            
            self.locationUpdateDelegate = locationUpdateDelegate
            
            self.locationManager = CLLocationManager()
            self.locationManager!.delegate = self
            self.locationManager!.desiredAccuracy = kCLLocationAccuracyBestForNavigation
            self.locationManager!.distanceFilter = 2
            self.locationManager!.pausesLocationUpdatesAutomatically = false
//            self.locationManager!.allowsBackgroundLocationUpdates = true
//            self.locationManager!.startUpdatingHeading()
            self.locationManager!.requestAlwaysAuthorization()
            self.locationManager!.startUpdatingLocation()
            self.locationManager!.startMonitoringSignificantLocationChanges()
        }
        
    }
    
    func locationManagerDidPauseLocationUpdates(_ manager: CLLocationManager) {
        
    }
    
    func locationManagerDidResumeLocationUpdates(_ manager: CLLocationManager) {
        
    }
    
    func locationManager(_ manager: CLLocationManager, didFailWithError error: Error) {
        
    }
    
    func locationManagerShouldDisplayHeadingCalibration(_ manager: CLLocationManager) -> Bool {
        return true
    }
    
    func locationManager(_ manager: CLLocationManager, didUpdateHeading newHeading: CLHeading) {
        
    }
    
    func locationManager(_ manager: CLLocationManager, didUpdateLocations locations: [CLLocation]) {
        
        let locationArray = locations as NSArray
        let locationObj = locationArray.lastObject as! CLLocation
        
        if(locationUpdateDelegate != nil){
            locationUpdateDelegate?.onLocationUpdate(location: locationObj)
        }
        
        if(isContinuous == false){
            releaseLocationTask()
        }
    }
    
    func locationManager(_ manager: CLLocationManager, didDetermineState state: CLRegionState, for region: CLRegion) {
        
    }
    
    func locationManager(_ manager: CLLocationManager, didChangeAuthorization status: CLAuthorizationStatus) {
        
    }
    
    func resumeLocationUpdates(){
        if(locationManager == nil){
            return
        }
        locationManager!.delegate = self
        locationManager!.startUpdatingHeading()
        locationManager!.startUpdatingLocation()
        locationManager!.startMonitoringSignificantLocationChanges()
    }
    
    func removeLocationUpdates(){
        if(locationManager == nil){
            return
        }
        locationManager!.delegate = nil
        locationManager!.stopUpdatingHeading()
        locationManager!.stopUpdatingLocation()
        locationManager!.stopMonitoringSignificantLocationChanges()
        
    }
    
    func releaseLocationTask(){
        
        removeLocationUpdates()
        locationUpdateDelegate = nil
        locationManager = nil
        
        if(locationUpdateDelegate != nil){
            locationUpdateDelegate = nil
        }
    }
}
