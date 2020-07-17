//
//  GetAddressFromLocation.swift
//  DriverApp
//
//  Created by NEW MAC on 29/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

protocol AddressFoundDelegate {
    func onAddressFound(address:String, location:CLLocation, isPickUpMode:Bool, dataResult:String)
}

class GetAddressFromLocation: NSObject {
    
    var uv:UIViewController!
    var location:CLLocation!
    
    var addressFoundDelegate:AddressFoundDelegate!
    
    let generalFunc = GeneralFunctions()
    
    var isPickUpMode = false
    
    init(uv:UIViewController, addressFoundDelegate:AddressFoundDelegate) {
        self.uv = uv
        self.addressFoundDelegate = addressFoundDelegate
        super.init()
    }
    
    func setLocation(latitude:Double, longitude:Double){
        self.location = CLLocation(latitude: latitude, longitude: longitude)
    }
    
    
    func setPickUpMode(isPickUpMode:Bool){
        self.isPickUpMode = isPickUpMode
    }
    
    func executeProcess(isOpenLoader:Bool, isAlertShow:Bool){
        if(location == nil){
            if(addressFoundDelegate != nil){
                addressFoundDelegate.onAddressFound(address: "", location: CLLocation(latitude: -180.0, longitude: -180.0
                ), isPickUpMode: self.isPickUpMode, dataResult: "")
            }
            return
        }
        
        let geoCodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng=\(location!.coordinate.latitude),\(location!.coordinate.longitude)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=true"
        
        //        print("geoCodeUrl:\(geoCodeUrl)")
        let exeWebServerUrl = ExeServerUrl(dict_data: [String:String](), currentView: self.uv.view, isOpenLoader: isOpenLoader)
        
        exeWebServerUrl.executeGetProcess(completionHandler: { (response) -> Void in
            
            //                        print("GeoCodeResponse:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("status").uppercased() != "OK" || dataDict.getArrObj("results").count == 0){
                    if(self.addressFoundDelegate != nil){
                        self.addressFoundDelegate.onAddressFound(address: "", location: self.location, isPickUpMode: self.isPickUpMode, dataResult: response)
                    }
                    return
                }
                
                let resultsArr = dataDict.getArrObj("results")
                
                let address = (resultsArr.object(at: 0) as! NSDictionary).get("formatted_address")
                
                let addressArr = address.characters.split{$0 == ","}.map(String.init)
                
                var finalAddress = ""
                
                for i in 0..<addressArr.count {
                    
                    if(addressArr[i].containsIgnoringCase(find: "Unnamed Road") == false && addressArr[i].isNumeric() == false){
                        finalAddress = finalAddress == "" ? addressArr[i] : (finalAddress + ", " + addressArr[i])
                    }
                }
                
                if(self.addressFoundDelegate != nil){
                    self.addressFoundDelegate.onAddressFound(address: finalAddress, location: self.location, isPickUpMode: self.isPickUpMode, dataResult: response)
                }
                
            }else{
                if(isAlertShow){
                    self.generalFunc.setError(uv: self.uv)
                }
                
            }
        }, url: geoCodeUrl)
    }
}
