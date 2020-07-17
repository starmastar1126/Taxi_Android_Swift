//
//  CommonUtils.swift
//  Login_SignUp
//
//  Created by Chirag on 08/12/15.
//  Copyright © 2015 ESW. All rights reserved.
//

import UIKit

class CommonUtils {
    
    
    static let appleAppId = "1385954708"
    

//    static let webServer: String = "http://192.168.1.131/fastcab/"
//    static let webServer: String = "http://webprojectsdemo.com/projects/fastcab/"
    static let webServer: String = "https://www.fastcab.co.za/"
    
    static var webservice_path: String = webServer+"webservice.php"
    
    
    static let google_geoCode_url: String = "https://maps.googleapis.com/maps/api/geocode/json"
    static let google_direction_url: String = "https://maps.googleapis.com/maps/api/directions/json"
    static let app_user_name = "Driver"
    
    static let user_image_url = webServer + "webimages/upload/Driver/"
    static let passenger_image_url = webServer + "webimages/upload/Passenger/"
        
}
