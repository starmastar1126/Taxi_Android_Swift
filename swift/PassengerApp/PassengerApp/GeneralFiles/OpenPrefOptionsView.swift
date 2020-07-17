//
//  OpenPrefOptionsView.swift
//  PassengerApp
//
//  Created by NEW MAC on 27/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenPrefOptionsView: NSObject, MyBtnClickDelegate {

    typealias CompletionHandler = (_ isPreferFemaleDriverEnable:Bool, _ isHandicapPrefEnabled:Bool) -> Void
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var currentInst:OpenPrefOptionsView!
    
    let generalFunc = GeneralFunctions()
    
    var openPrefOptionView:PreferencesOptionView!
    var openPrefOptionBGView:UIView!
    var handler:CompletionHandler!
    
    var isPreferFemaleDriverEnable = false
    var isHandicapPrefEnabled = false
    
    
    init(uv:UIViewController, containerView:UIView){
        self.uv = uv
        self.containerView = containerView
        super.init()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(){
        
        let width = (Application.screenSize.width - 20) > 380 ? 370 : (Application.screenSize.width - 50)
        var height:CGFloat = 280
        
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let HANDICAP_ACCESSIBILITY_OPTION = userProfileJson.get("HANDICAP_ACCESSIBILITY_OPTION")
        let FEMALE_RIDE_REQ_ENABLE = userProfileJson.get("FEMALE_RIDE_REQ_ENABLE")
        
        if(HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES"){
            height = height - 60
//            handiCapView.isHidden = true
//            handiCapViewHeight.constant = 0
        }
        
        if(FEMALE_RIDE_REQ_ENABLE.uppercased() != "YES" || userProfileJson.get("eGender") != "Female"){
            height = height - 60
            
//            genderView.isHidden = true
//            genderViewHeight.constant = 0
        }
        
        openPrefOptionView = PreferencesOptionView(frame: CGRect(x: (Application.screenSize.width / 2) - (width / 2), y: (Application.screenSize.height / 2) - (height / 2), width: width, height: height))
        
        
        let bgView = UIView()
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.isUserInteractionEnabled = true
        
        let bgTapGue = UITapGestureRecognizer()
        bgTapGue.addTarget(self, action: #selector(self.removeView))
        bgView.addGestureRecognizer(bgTapGue)
        
        self.openPrefOptionBGView = bgView
        
        openPrefOptionView.layer.shadowOpacity = 0.5
        openPrefOptionView.layer.shadowOffset = CGSize(width: 0, height: 3)
        openPrefOptionView.layer.shadowColor = UIColor.black.cgColor
        
        //        self.view.addSubview(bgView)
        //        self.view.addSubview(bookingFinishView)
        
        let currentWindow = Application.window
        
        if(currentWindow != nil){
            currentWindow?.addSubview(bgView)
            currentWindow?.addSubview(openPrefOptionView)
        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(openPrefOptionView)
        }
        
        Utils.createRoundedView(view: openPrefOptionView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        self.openPrefOptionView.prefHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Preferences", key: "LBL_PREFRANCE_TXT")
        self.openPrefOptionView.setPrefBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_UPDATE"))
        self.openPrefOptionView.genderLbl.text = self.generalFunc.getLanguageLabel(origValue: "Prefer Female Driver", key: "LBL_ACCEPT_FEMALE_REQ_ONLY_PASSENGER")
        self.openPrefOptionView.genderLbl.fitText()
        
        self.openPrefOptionView.handiCaplbl.text = self.generalFunc.getLanguageLabel(origValue: "Prefer Taxis with Handicap Accessibility", key: "LBL_MUST_HAVE_HANDICAP_ASS_CAR")
        self.openPrefOptionView.handiCaplbl.fitText()
        
        self.openPrefOptionView.setPrefBtn.clickDelegate = self
        
        if(isPreferFemaleDriverEnable == true){
            self.openPrefOptionView.genderChkBox.on = true
        }
        
        if(isHandicapPrefEnabled == true){
            self.openPrefOptionView.handiCapChkBox.on = true
        }
    
        if(HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES"){
            height = height - 60
            self.openPrefOptionView.handiCapView.isHidden = true
            self.openPrefOptionView.handiCapViewHeight.constant = 0
        }
        
        if(FEMALE_RIDE_REQ_ENABLE.uppercased() != "YES" || userProfileJson.get("eGender") != "Female"){
            self.openPrefOptionView.genderView.isHidden = true
            self.openPrefOptionView.genderViewHeight.constant = 0

        }
    }
    
    func removeView(){
        openPrefOptionView.frame.origin.y = Application.screenSize.height + 2500
        openPrefOptionView.removeFromSuperview()
        openPrefOptionBGView.removeFromSuperview()
        
        self.uv.view.layoutIfNeeded()
    }
    
    func myBtnTapped(sender: MyButton) {
        if(self.openPrefOptionView != nil && self.openPrefOptionView.setPrefBtn != nil && self.openPrefOptionView.setPrefBtn == sender){
            removeView()
            if(self.handler != nil){
                self.handler(self.openPrefOptionView.genderChkBox.on, self.openPrefOptionView.handiCapChkBox.on)
            }
        }
    }
}
