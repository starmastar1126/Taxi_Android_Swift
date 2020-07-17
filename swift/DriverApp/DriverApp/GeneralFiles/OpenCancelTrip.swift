//
//  OpenCancelTrip.swift
//  DriverApp
//
//  Created by NEW MAC on 29/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

protocol OnTripCanceledDelegate {
    func onTripCanceled(reason:String, comment:String, openCancelTrip:OpenCancelTrip)
    func onTripViewClosed(openCancelTrip:OpenCancelTrip)
}

class OpenCancelTrip: NSObject {
    var uv:UIViewController!
    
    var containerView:UIView!
    
    let generalFunc = GeneralFunctions()
    
    var tripData:NSDictionary!
    
    var bgView:UIView!
    var cancelTripView:UIView!
    
    var currInst:OpenCancelTrip!
    
    var onTripCanceledDelegate:OnTripCanceledDelegate!
    
    init(uv:UIViewController, containerView:UIView){
        self.uv = uv
        self.containerView = containerView
        super.init()
    }
    
    func cancelTrip(){
        
        let cancelTripView = self.generalFunc.loadView(nibName: "CancelTripView", uv: self.uv, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 375 ? 365 : Application.screenSize.width - 50
        
        cancelTripView.frame.size = CGSize(width: width, height: 315)
        
        
        cancelTripView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        let bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.isUserInteractionEnabled = true
        
        let bgViewTapGue = UITapGestureRecognizer()
//        bgView.frame = self.containerView.frame
        
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        bgViewTapGue.addTarget(currInst, action: #selector(currInst.closeView))
        self.bgView = bgView
        bgView.addGestureRecognizer(bgViewTapGue)
        
        
        self.cancelTripView = cancelTripView
        
        cancelTripView.layer.shadowOpacity = 0.5
        cancelTripView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cancelTripView.layer.shadowColor = UIColor.black.cgColor
        
        let currentWindow = Application.window
        
        if(currentWindow != nil){
            currentWindow?.addSubview(bgView)
            currentWindow?.addSubview(cancelTripView)
        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(cancelTripView)
        }
        
//        self.uv.view.addSubview(bgView)
//        self.uv.view.addSubview(cancelTripView)
        
        Utils.createRoundedView(view: cancelTripView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        (cancelTripView.subviews[0] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_CANCEL_DELIVERY" : "LBL_CANCEL_TRIP")
        
        (cancelTripView.subviews[1] as! MyTextField).setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ENTER_REASON"))
        (cancelTripView.subviews[2] as! MyTextField).setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WRITE_COMMENT_HINT_TXT"))
        
        (cancelTripView.subviews[4].subviews[0] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "Cancel delivery now" : "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_CANCEL_DELIVERY_NOW" : "LBL_CANCEL_TRIP_NOW")
        (cancelTripView.subviews[4].subviews[1] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "Continue delivery" : "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_CONTINUE_DELIVERY" : "LBL_CONTINUE_TRIP_TXT")
        
        
        let okTapGue = UITapGestureRecognizer()
        let cancelTapGue = UITapGestureRecognizer()
        
        okTapGue.addTarget(self, action: #selector(self.okTapped))
        cancelTapGue.addTarget(self, action: #selector(self.cancelTapped))
        
        (cancelTripView.subviews[4].subviews[0]).isUserInteractionEnabled = true
        (cancelTripView.subviews[4].subviews[1]).isUserInteractionEnabled = true
        
        (cancelTripView.subviews[4].subviews[0]).addGestureRecognizer(okTapGue)
        (cancelTripView.subviews[4].subviews[1]).addGestureRecognizer(cancelTapGue)

    }
    
    func closeView(){
        
        bgView.removeFromSuperview()
        cancelTripView.removeFromSuperview()
        
        if(onTripCanceledDelegate != nil){
            onTripCanceledDelegate!.onTripViewClosed(openCancelTrip:currInst)
        }
    }
    
    func setDelegate(onTripCanceledDelegate:OnTripCanceledDelegate?){
        self.onTripCanceledDelegate = onTripCanceledDelegate
    }
    
    func okTapped(){
        let reason = Utils.getText(textField: (cancelTripView.subviews[1] as! MyTextField).getTextField()!)
        let comment = Utils.getText(textField: (cancelTripView.subviews[2] as! MyTextField).getTextField()!)
        
        let reasonEntered = reason == "" ? Utils.setErrorFields(textField: (cancelTripView.subviews[1] as! MyTextField).getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")) : true
        
        if(reasonEntered == true){
        
            if(onTripCanceledDelegate != nil){
                onTripCanceledDelegate!.onTripCanceled(reason: reason, comment: comment, openCancelTrip: currInst)
            }
            
            
            closeView()
            
        }
    }
    
    func cancelTapped(){
        
        closeView()
    }
}
