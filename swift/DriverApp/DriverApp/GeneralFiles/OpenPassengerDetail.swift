//
//  OpenPassengerDetail.swift
//  DriverApp
//
//  Created by NEW MAC on 27/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenPassengerDetail: NSObject {
    
    var uv:UIViewController!
    
    let generalFunc = GeneralFunctions()
    
    var tripData:NSDictionary!
    
    var bgView:UIView!
    var passengerDetailView:UIView!
    
    var currInst:OpenPassengerDetail!
    var containerView:UIView!
    
    var navZposition:CGFloat = 0
    
    init(uv:UIViewController, containerView:UIView){
        self.uv = uv
        self.containerView = containerView
        super.init()
    }
    
    func showDetail(){
    
        let passengerDetailView = self.generalFunc.loadView(nibName: "OpenPassengerDetailView", uv: self.uv, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 385 ? 365 : Application.screenSize.width - 50
        
        passengerDetailView.frame.size = CGSize(width: width, height: 260)
        
        
        passengerDetailView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        let bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.isUserInteractionEnabled = true
        
        let bgViewTapGue = UITapGestureRecognizer()
//        bgView.frame = self.containerView.frame
//        bgView.frame.size = CGSize(width: bgView.frame.width, height: Application.screenSize.height)
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)

        bgViewTapGue.addTarget(currInst, action: #selector(currInst.closeView))
        self.bgView = bgView
        bgView.addGestureRecognizer(bgViewTapGue)
        
        
        self.passengerDetailView = passengerDetailView
        
        passengerDetailView.layer.shadowOpacity = 0.5
        passengerDetailView.layer.shadowOffset = CGSize(width: 0, height: 3)
        passengerDetailView.layer.shadowColor = UIColor.black.cgColor
        
        let currentWindow = Application.window
        
        if(self.uv == nil){
            currentWindow?.addSubview(bgView)
            currentWindow?.addSubview(passengerDetailView)
        }else if(self.uv.navigationController != nil){
            self.uv.navigationController?.view.addSubview(bgView)
            self.uv.navigationController?.view.addSubview(passengerDetailView)
            
            navZposition = self.uv.navigationController!.navigationBar.layer.zPosition
            self.uv.navigationController?.navigationBar.layer.zPosition = -1
            
            passengerDetailView.tag = Utils.ALERT_DIALOG_CONTENT_TAG
            bgView.tag = Utils.ALERT_DIALOG_BG_TAG
        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(passengerDetailView)
        }
        
        Utils.createRoundedView(view: passengerDetailView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        Utils.createRoundedView(view: passengerDetailView.subviews[3], borderColor: UIColor.clear, borderWidth: 0)
        
        (passengerDetailView.subviews[0] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PASSENGER_DETAIL")
        
        (passengerDetailView.subviews[1] as! MyLabel).text = self.tripData!.get("PName")
        (passengerDetailView.subviews[2] as! MyLabel).text = Configurations.convertNumToAppLocal(numStr: self.tripData!.get("PRating"))
        
        (passengerDetailView.subviews[3] as! UIImageView).sd_setImage(with: URL(string: CommonUtils.passenger_image_url + tripData!.get("PassengerId") + "/" + tripData!.get("PPicName")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        (passengerDetailView.subviews[5] as! RatingView).rating = GeneralFunctions.parseFloat(origValue: 0.0, data: self.tripData!.get("PRating"))
        
        (passengerDetailView.subviews[7].subviews[0].subviews[0].subviews[1] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CALL_TXT")
        (passengerDetailView.subviews[7].subviews[1].subviews[0].subviews[1] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MESSAGE_TXT")
        
        GeneralFunctions.setImgTintColor(imgView: passengerDetailView.subviews[7].subviews[0].subviews[0].subviews[0] as! UIImageView, color: UIColor(hex: 0x000000))
        GeneralFunctions.setImgTintColor(imgView: passengerDetailView.subviews[7].subviews[1].subviews[0].subviews[0] as! UIImageView, color: UIColor(hex: 0x000000))
        
        let calltapGue = UITapGestureRecognizer()
        let msgTapGue = UITapGestureRecognizer()
        
        calltapGue.addTarget(self, action: #selector(self.callTapped))
        msgTapGue.addTarget(self, action: #selector(self.msgTapped))
        
        (passengerDetailView.subviews[7].subviews[0]).isUserInteractionEnabled = true
        (passengerDetailView.subviews[7].subviews[1]).isUserInteractionEnabled = true
        
        (passengerDetailView.subviews[7].subviews[0]).addGestureRecognizer(calltapGue)
        (passengerDetailView.subviews[7].subviews[1]).addGestureRecognizer(msgTapGue)
    }

    func closeView(){
    
        bgView.removeFromSuperview()
        passengerDetailView.removeFromSuperview()
        
        if(self.uv != nil){
            self.uv.navigationController?.navigationBar.layer.zPosition = navZposition
        }
    }
    
    func callTapped(){
        
        let number = "\(self.tripData!.get("PPhone"))"

        UIApplication.shared.openURL(NSURL(string:"telprompt:" + number)! as URL)
    }
    
  
    
    func msgTapped(){
        
        closeView()
        
        let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
        
        chatUV.receiverId = tripData!.get("PassengerId")
        chatUV.receiverDisplayName = self.tripData!.get("PName")
        chatUV.assignedtripId = self.tripData!.get("TripId")
        chatUV.pPicName = self.tripData!.get("PPicName")
        self.uv.pushToNavController(uv:chatUV, isDirect: true)
        
    }
}
