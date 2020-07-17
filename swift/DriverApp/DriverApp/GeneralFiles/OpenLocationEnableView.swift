//
//  OpenLocationEnableView.swift
//  DriverApp
//
//  Created by NEW MAC on 27/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps

class OpenLocationEnableView: NSObject, MyLabelClickDelegate, OnLocationUpdateDelegate {

    var uv:UIViewController!
    var containerView:UIView!
    
    var getLocation:GetLocation!
    
    var currentInst:OpenLocationEnableView!
    
    let generalFunc = GeneralFunctions()
    var bgView:UIView!
    
    var gMapView:GMSMapView?
    var isMapLocEnabled = false
    
    var enableLocationView:EnableLocationView!
    
    init(uv:UIViewController, containerView:UIView, gMapView:GMSMapView?, isMapLocEnabled:Bool){
        self.uv = uv
        self.containerView = containerView
        self.gMapView = gMapView
        self.isMapLocEnabled = isMapLocEnabled
        super.init()
    }
    
    func addBackgroundObserver(){
        NotificationCenter.default.removeObserver(currentInst, name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
        NotificationCenter.default.addObserver(currentInst, selector: #selector(currentInst.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func show(){
        
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width: self.containerView.frame.width, height: self.containerView.frame.height)
        
        bgView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        let width = (self.containerView.frame.width > 390 ? 375 : (self.containerView.frame.width - 50))
        let extraHeight = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_IPHONE_TXT").height(withConstrainedWidth: width - 30, font: UIFont(name: "Roboto-Light", size: 16)!) - 20
        
        enableLocationView = EnableLocationView(frame: CGRect(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2, width: width, height: 140 + extraHeight))
        
        enableLocationView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        if(InternetConnection.isConnectedToNetwork() == false)
        {
            enableLocationView.locHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Internet Connection", key: "LBL_NO_INTERNET_TITLE")
            enableLocationView.locHLbl.fitText()
            
            enableLocationView.locSubLbl.text = self.generalFunc.getLanguageLabel(origValue: "Application requires internet connection to be enabled. Please check your network settings.", key: "LBL_NO_INTERNET_SUB_TITLE")
            enableLocationView.locSubLbl.fitText()
            
            enableLocationView.positiveLbl.text = self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT")
            enableLocationView.negativeLbl.text = self.generalFunc.getLanguageLabel(origValue: "Settings", key: "LBL_SETTINGS")
             //enableLocationView.negativeLbl.isHidden = true
            
        }
        else
        {
            enableLocationView.negativeLbl.isHidden = false
            
            enableLocationView.locHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Enable Location Service", key: "LBL_ENABLE_LOC_SERVICE")
            enableLocationView.locSubLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_IPHONE_TXT")
            enableLocationView.locSubLbl.fitText()
            
            enableLocationView.positiveLbl.text = self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT")
            enableLocationView.negativeLbl.text = self.generalFunc.getLanguageLabel(origValue: "Settings", key: "LBL_SETTINGS")
        }
        enableLocationView.positiveLbl.setClickDelegate(clickDelegate: self)
        enableLocationView.negativeLbl.setClickDelegate(clickDelegate: self)
        
        Utils.createRoundedView(view: enableLocationView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        enableLocationView.layer.shadowOpacity = 0.5
        enableLocationView.layer.shadowOffset = CGSize(width: 0, height: 3)
        enableLocationView.layer.shadowColor = UIColor.black.cgColor
        
        
//        let currentWindow = Application.window
        
//        if(currentWindow != nil){
//            currentWindow?.addSubview(bgView)
//            currentWindow?.addSubview(enableLocationView)
//            currentWindow?.addSubview(navBar)
//        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(enableLocationView)
//            currentWindow?.addSubview(navBar)
//        }
        
        getLocation = GetLocation(uv: self.uv, isContinuous: false)
        getLocation.buildLocManager(locationUpdateDelegate: self)


    }
    
    func myLableTapped(sender: MyLabel) {
        if(InternetConnection.isConnectedToNetwork() == false)
        {
            if(sender == enableLocationView.positiveLbl){
                if(InternetConnection.isConnectedToNetwork() == true){
                    removeView()
                }
            }else if(sender == enableLocationView.negativeLbl){
                
                if #available(iOS 10.0, *) {
                    UIApplication.shared.open(URL(string:"App-Prefs:root=Settings")!, options: [:], completionHandler: nil)
                } else {
                    UIApplication.shared.openURL(URL(string:UIApplicationOpenSettingsURLString)!)
                }
                
            }
        }else if(enableLocationView != nil){
            if(sender == enableLocationView.positiveLbl){
                if(GeneralFunctions.hasLocationEnabled()){
                    removeView()
                }
            }else if(sender == enableLocationView.negativeLbl){
                
                if (CLLocationManager.locationServicesEnabled()  == false) {
                    if let url = URL(string: "App-Prefs:root=Privacy&path=LOCATION") {
                        // If general location settings are disabled then open general location settings
                        UIApplication.shared.openURL(url)
                    }
                } else {
                    if let url = URL(string: UIApplicationOpenSettingsURLString) {
                        // If general location settings are enabled then open location settings for the app
                        UIApplication.shared.openURL(url)
                    }
                }
                
            }
        }
    }
    
    func removeView(){
        enableLocationView.frame.origin.y = Application.screenSize.height + 2500
        
        bgView.removeFromSuperview()
        
        self.uv.view.layoutIfNeeded()
    }
    
    func appInForground(){
    
    }

}
