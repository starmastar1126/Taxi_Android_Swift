//
//  ExtCLLocation.swift
//  DriverApp
//
//  Created by NEW MAC on 22/11/16.
//  Copyright © 2016 BBCS. All rights reserved.
//

import Foundation
import CoreLocation
public extension CLLocation{
    
    func DegreesToRadians(degrees: Double ) -> Double {
        return degrees * M_PI / 180
    }
    
    func RadiansToDegrees(radians: Double) -> Double {
        return radians * 180 / M_PI
    }
    
    
    func bearingToLocationRadian(destinationLocation:CLLocation,currentRotation:Double) -> Double {
        
//        if (destinationLocation.distance(from: self) > 3)
//        {
        
            
            let lat1 = DegreesToRadians(degrees: self.coordinate.latitude)
            let lon1 = DegreesToRadians(degrees: self.coordinate.longitude)
            
            let lat2 = DegreesToRadians(degrees: destinationLocation.coordinate.latitude);
            let lon2 = DegreesToRadians(degrees: destinationLocation.coordinate.longitude);
            
            let dLon = lon2 - lon1
            
            let y = sin(dLon) * cos(lat2);
            let x = cos(lat1) * sin(lat2) - sin(lat1) * cos(lat2) * cos(dLon);
            let radiansBearing = atan2(y, x)
            
            return radiansBearing
            
            
//        }
//        else
//        {
////            return currentRotation * M_PI / 180
//            return -1
//        }
        
    }
    
    func bearingToLocationDegrees(destinationLocation:CLLocation,currentRotation:Double) -> Double{
//        return   RadiansToDegrees(radians: bearingToLocationRadian(destinationLocation: destinationLocation, currentRotation: currentRotation))
        if (destinationLocation.distance(from: self) > 1){
            return RadiansToDegrees(radians: bearingToLocationRadian(destinationLocation: destinationLocation, currentRotation: currentRotation))
        }else{
            return -1
        }
    }
}
