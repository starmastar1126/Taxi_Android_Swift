//
//  OpenEnableLocationView.swift
//  PassengerApp
//
//  Created by NEW MAC on 28/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenEnableLocationView: NSObject, MyBtnClickDelegate, OnLocationUpdateDelegate {

    typealias CompletionHandler = (_ latitude:Double, _ longitude:Double, _ address:String, _ isMenuOpen:Bool) -> Void
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var currentInst:OpenEnableLocationView!
    
    let generalFunc = GeneralFunctions()
    
    var enableLocationView:EnableLocationView!
    var enableLocationBGView:UIView!
    var handler:CompletionHandler!
    
    var getLocation:GetLocation!
    
    var menuImgView:UIImageView!
    
    init(uv:UIViewController, containerView:UIView, menuImgView:UIImageView){
        self.uv = uv
        self.containerView = containerView
        self.menuImgView = menuImgView
        super.init()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(){
        let width = Application.screenSize.width
        let height = self.containerView.frame.height
        
        enableLocationView = EnableLocationView(frame: CGRect(x:0, y:0, width: width, height: height - GeneralFunctions.getSafeAreaInsets().bottom ))
        
        
        enableLocationView.frame.size = CGSize(width: width, height: height)
        
        let menuImage = menuImgView!.image
        menuImgView = UIImageView(frame: CGRect(x: menuImgView.frame.minX, y: menuImgView.frame.minY, width: menuImgView.frame.width, height: menuImgView.frame.width))
        menuImgView.image = menuImage
        
        if(InternetConnection.isConnectedToNetwork() == false){
            menuImgView.image = UIImage(named: "ic_menu_all")!
            enableLocationView.iconImgView.image = UIImage(named: "ic_network_off")!
        }
        
        GeneralFunctions.setImgTintColor(imgView: menuImgView, color: UIColor(hex: 0xFFFFFF))
        GeneralFunctions.setImgTintColor(imgView: enableLocationView.iconImgView, color: UIColor(hex: 0xFFFFFF))
        
        enableLocationView.center = CGPoint(x: Application.screenSize.width / 2, y: height / 2)
        
        let bgView = UIView()
        
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: height + GeneralFunctions.getSafeAreaInsets().bottom )
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: (height + GeneralFunctions.getSafeAreaInsets().bottom) / 2)
        
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.80
        bgView.isUserInteractionEnabled = true
        
        self.enableLocationBGView = bgView
        
        
        //        self.view.addSubview(bgView)
        //        self.view.addSubview(bookingFinishView)
        
//        let currentWindow = Application.window
//        
//        if(currentWindow != nil){
//            currentWindow?.addSubview(bgView)
//            currentWindow?.addSubview(enableLocationView)
//        }else{
//            let window = UIApplication.shared.keyWindow!
//            window.addSubview(bgView)
//            window.addSubview(enableLocationView)
//            window.addSubview(menuImgView)
            self.containerView.addSubview(bgView)
            self.containerView.addSubview(enableLocationView)
            self.containerView.addSubview(menuImgView)
//        }
        
        enableLocationView.turnOnBtn.clickDelegate = self
        enableLocationView.enterPickUpBtn.clickDelegate = self
        
        enableLocationView.turnOnBtn.enableCustomColor()
        
        enableLocationView.enterPickUpBtn.enableCustomColor()
        
        getLocation = GetLocation(uv: self.uv, isContinuous: false)
        getLocation.buildLocManager(locationUpdateDelegate: self)
        
        let menuImgTapGue = UITapGestureRecognizer()
        menuImgView.isUserInteractionEnabled = true
        menuImgTapGue.addTarget(self, action: #selector(currentInst.openMenu))
        menuImgView.addGestureRecognizer(menuImgTapGue)
        
//        NotificationCenter.default.addObserver(currentInst, selector: #selector(currentInst.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func openMenu(){
        if(self.handler != nil){
            self.handler(0.0, 0.0, "", true)
        }
    }
    
    func closeView(){
        menuImgView.removeFromSuperview()
        enableLocationView.removeFromSuperview()
        enableLocationBGView.removeFromSuperview()
        
//        GeneralFunctions.removeObserver(obj: self.currentInst)
    }
    
    func myBtnTapped(sender: MyButton) {
        if(InternetConnection.isConnectedToNetwork() == false)
        {
            if #available(iOS 10.0, *) {
                UIApplication.shared.open(URL(string:"App-Prefs:root=Settings")!, options: [:], completionHandler: nil)
            } else {
                UIApplication.shared.openURL(URL(string:UIApplicationOpenSettingsURLString)!)
            }
        }else if(self.enableLocationView != nil && sender == self.enableLocationView.turnOnBtn){
            
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
            
        }else if(self.enableLocationView != nil && sender == self.enableLocationView.enterPickUpBtn){
            launchPlaceFinder(centerLocation: CLLocation())
        }
    }
    
    func launchPlaceFinder(centerLocation:CLLocation){
        let launchPlaceFinder = LaunchPlaceFinder(viewControllerUV: self.uv)
        launchPlaceFinder.currInst = launchPlaceFinder
        
        launchPlaceFinder.setBiasLocation(sourceLocationPlaceLatitude: centerLocation.coordinate.latitude, sourceLocationPlaceLongitude: centerLocation.coordinate.longitude)
        
        launchPlaceFinder.initializeFinder { (address, latitude, longitude) in
            
            if(self.handler != nil){
                self.handler(latitude, longitude, address, false)
            }
            
        }
    }
}
