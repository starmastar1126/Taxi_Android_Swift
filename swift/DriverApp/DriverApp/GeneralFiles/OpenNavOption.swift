//
//  OpenNavOption.swift
//  DriverApp
//
//  Created by NEW MAC on 22/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenNavOption: NSObject {
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var placeLatitude = ""
    var placeLongitude = ""
    
    let generalFunc = GeneralFunctions()
    var navigationOptionView:NavigationOptionView!
    var bgView:UIView!
    
    init(uv:UIViewController, containerView:UIView, placeLatitude:String, placeLongitude:String){
        self.uv = uv
        self.containerView = containerView
        self.placeLatitude = placeLatitude
        self.placeLongitude = placeLongitude
        super.init()
    }
    
    func chooseOption(){
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.frame = self.containerView.frame
        
        let bgTapGue = UITapGestureRecognizer()
        bgTapGue.addTarget(self, action: #selector(self.removeView))
        bgView.addGestureRecognizer(bgTapGue)
        
        bgView.center = CGPoint(x: self.uv.view.bounds.midX, y: self.uv.view.bounds.midY)
        
         navigationOptionView = NavigationOptionView(frame: CGRect(x: self.uv.view.frame.width / 2, y: self.uv.view.frame.height / 2, width: (Application.screenSize.width > 390 ? 375 : (Application.screenSize.width - 50)), height: 250))
        navigationOptionView.center = CGPoint(x: self.uv.view.bounds.midX, y: self.uv.view.bounds.midY)
        navigationOptionView.setHandler { (view, optionId) in
            
            
            self.removeView()
            
            if(optionId == 0){
                if(UIApplication.shared.canOpenURL(NSURL(string: "comgooglemaps-x-callback://")! as URL)){
                    
                    
                    var displayName = Bundle.main.displayName != nil ? String(describing: Bundle.main.displayName!) : ""
                    displayName = displayName.replace(" ", withString: "")
                    
                    let urlToOpen = "comgooglemaps-x-callback://?daddr=\(self.placeLatitude),\(self.placeLongitude)&nav=1&x-success=sourceapp://?resume=true&x-source=\(displayName)&directionsmode=driving"
                    UIApplication.shared.openURL(NSURL(string: urlToOpen.addingPercentEncoding(withAllowedCharacters: .urlFragmentAllowed)! )! as URL)
                    
                }else{
                    
                    self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Please install Google Maps in your device.", key: "LBL_INSTALL_GOOGLE_MAPS"))
                    
                }
            }else if(optionId == 1){
                
                if(UIApplication.shared.canOpenURL(NSURL(string: "waze://")! as URL)){
                    
                    UIApplication.shared.openURL(NSURL(string:
                        "waze://?ll=\(self.placeLatitude),\(self.placeLongitude)&navigate=yes")! as URL)
                   
                }else{
                    
                    self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Please install Waze navigation app in your device.", key: "LBL_INSTALL_WAZE"))
                    
                }
                
            }
        }
        
        Utils.createRoundedView(view: navigationOptionView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        navigationOptionView.layer.shadowOpacity = 0.5
        navigationOptionView.layer.shadowOffset = CGSize(width: 0, height: 3)
        navigationOptionView.layer.shadowColor = UIColor.black.cgColor
        
        self.uv.view.addSubview(bgView)
        self.uv.view.addSubview(navigationOptionView)
    }

    func removeView(){
        navigationOptionView.frame.origin.y = Application.screenSize.height + 2500
        
        bgView.removeFromSuperview()
        
        self.uv.view.layoutIfNeeded()
    }
}
