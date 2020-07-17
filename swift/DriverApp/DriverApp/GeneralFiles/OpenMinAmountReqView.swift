//
//  OpenMinAmountReqView.swift
//  DriverApp
//
//  Created by NEW MAC on 24/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenMinAmountReqView: NSObject, MyLabelClickDelegate {
    typealias CompletionHandler = (_ isSkipped:Bool, _ isOpenWallet:Bool, _ view:UIView, _ bgView:UIView) -> Void
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var currentInst:OpenMinAmountReqView!
    
    let generalFunc = GeneralFunctions()
    var bgView:UIView!
    
    var minimumAmountReqView:MinimumAmountReqView!
    
    var handler:CompletionHandler!
    var userProfileJson:NSDictionary!
    
    init(uv:UIViewController, containerView:UIView){
        self.uv = uv
        self.containerView = containerView
        super.init()
    }
    
    func setHandler(handler:@escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(msg:String){
        
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width: self.containerView.frame.width, height: self.containerView.frame.height)
        
        bgView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        let width = (self.containerView.frame.width > 390 ? 375 : (self.containerView.frame.width - 50))
        let extraHeight = msg.height(withConstrainedWidth: width - 30, font: UIFont(name: "Roboto-Light", size: 18)!) - 20
        
        minimumAmountReqView = MinimumAmountReqView(frame: CGRect(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2, width: width, height: 250 + extraHeight))
        
        minimumAmountReqView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        minimumAmountReqView.subLbl.text = msg
        minimumAmountReqView.subLbl.fitText()
        
        minimumAmountReqView.hLbl.text = self.generalFunc.getLanguageLabel(origValue: "Low Balance", key: "LBL_LOW_BALANCE")
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        if(userProfileJson.get("APP_PAYMENT_MODE").uppercased() == "CASH"){
            minimumAmountReqView.poitiveLbl.text = self.generalFunc.getLanguageLabel(origValue: "CONTACT US", key: "LBL_CONTACT_US_TXT").uppercased()
        }else{
            minimumAmountReqView.poitiveLbl.text = self.generalFunc.getLanguageLabel(origValue: "ADD NOW", key: "LBL_ADD_NOW").uppercased()
        }
        minimumAmountReqView.negativeLbl.text = self.generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT").uppercased()
        
        minimumAmountReqView.poitiveLbl.setClickDelegate(clickDelegate: self)
        minimumAmountReqView.negativeLbl.setClickDelegate(clickDelegate: self)
        
        Utils.createRoundedView(view: minimumAmountReqView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        minimumAmountReqView.layer.shadowOpacity = 0.5
        minimumAmountReqView.layer.shadowOffset = CGSize(width: 0, height: 3)
        minimumAmountReqView.layer.shadowColor = UIColor.black.cgColor
        
        
        self.uv.view.addSubview(bgView)
        self.uv.view.addSubview(minimumAmountReqView)
        
        bgView.alpha = 0
        minimumAmountReqView.alpha = 0
        UIView.animate(
            withDuration: 0.3,
            delay: 0,
            options: .curveEaseInOut,
            animations: {
                self.bgView.alpha = 0.4
                self.minimumAmountReqView.alpha = 1
                
        }
        )
    }
    
    func closeView(){
        minimumAmountReqView.frame.origin.y = Application.screenSize.height + 2500
        minimumAmountReqView.removeFromSuperview()
        bgView.removeFromSuperview()
        
        self.uv.view.layoutIfNeeded()
    }
    
    func myLableTapped(sender: MyLabel) {
        if(minimumAmountReqView != nil){
            if(sender == minimumAmountReqView.poitiveLbl){
                if(handler != nil){
                    handler(false, true, minimumAmountReqView, bgView)
                }
                
                self.closeView()
            }else if(sender == minimumAmountReqView.negativeLbl){
                
                if(handler != nil){
                    handler(true, false, minimumAmountReqView, bgView)
                }
                
                self.closeView()
            }
        }
    }
}
