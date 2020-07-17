//
//  OpenProviderDetailView.swift
//  PassengerApp
//
//  Created by NEW MAC on 02/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenProviderDetailView: NSObject {
    typealias CompletionHandler = (_ isContinueBtnTapped:Bool) -> Void
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var currentInst:OpenProviderDetailView!
    
    let generalFunc = GeneralFunctions()
    
    var providerDetailDesignView:ProviderDetailView!
    var providerDetailDesignBGView:UIView!
    
    var handler:CompletionHandler!
    
    init(uv:UIViewController, containerView:UIView){
        self.uv = uv
        self.containerView = containerView
        super.init()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(dataDict:NSDictionary, eUnit:String){
//        let width = (Application.screenSize.width - 50) > 375 ? 375 : Application.screenSize.width - 50
//        let desHeight = dataDict.get("tProfileDescription").height(withConstrainedWidth: width - 30, font: UIFont.systemFont(ofSize: 16))
//        let height = (350 + desHeight) > 450 ? 460 : (350 + desHeight)
//
        
        let width = Application.screenSize.width
        let desHeight = dataDict.get("tProfileDescription").height(withConstrainedWidth: width - 30, font: UIFont.systemFont(ofSize: 16))
        let height = Application.screenSize.height
        
        providerDetailDesignView = ProviderDetailView(frame: CGRect(x:0, y:0, width: width, height: height))
        
        providerDetailDesignView.frame.size = CGSize(width: width, height: height)
        
        providerDetailDesignView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        providerDetailDesignView.closeImgViewTopMargin.constant = 25 + (GeneralFunctions.getSafeAreaInsets().top / 2)
        providerDetailDesignView.providerPicTopMargin.constant = 75 + (GeneralFunctions.getSafeAreaInsets().top / 2)
        providerDetailDesignView.headerViewHeight.constant = 135 + (GeneralFunctions.getSafeAreaInsets().top / 2)
        providerDetailDesignView.scrollContentViewHeight.constant = 340 + desHeight +  (GeneralFunctions.getSafeAreaInsets().top / 2)
        
        providerDetailDesignView.scrollView.contentSize = CGSize(width: width, height: (340 + desHeight) +  (GeneralFunctions.getSafeAreaInsets().top / 2))
        providerDetailDesignView.scrollView.bounces = false
        let bgView = UIView()
        
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.80
        bgView.isUserInteractionEnabled = true
        
        self.providerDetailDesignBGView = bgView
        
        let currentWindow = Application.window
        
        if(currentWindow != nil){
            currentWindow?.addSubview(bgView)
            currentWindow?.addSubview(providerDetailDesignView)
        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(providerDetailDesignView)
        }
        
//        providerDetailDesignView.providerDetailTxtViewHeight.constant = desHeight > 120 ? 120 : desHeight
        
        providerDetailDesignView.setViewHandler { (view, isViewClose, isContinueBtnTapped) in
            
            self.closeView()
            if(isContinueBtnTapped){
                if(self.handler != nil){
                    if(self.uv .isKind(of: MainScreenUV.self)){
                        (self.uv as! MainScreenUV).ufxSelectedServiceProviderId = dataDict.get("driver_id")
                    }
                    self.handler(true)
                }
            }
        }
        
        if(dataDict.get("fAmount") != ""){
            providerDetailDesignView.priceLbl.text = Configurations.convertNumToAppLocal(numStr: dataDict.get("fAmount"))
            providerDetailDesignView.priceLbl.isHidden = false
        }else{
            providerDetailDesignView.priceLbl.isHidden = true
        }
        
        if(dataDict.get("PROVIDER_RATING_COUNT") != ""){
            providerDetailDesignView.ratingCountLbl.text = "\(Configurations.convertNumToAppLocal(numStr: dataDict.get("PROVIDER_RATING_COUNT"))) \(self.generalFunc.getLanguageLabel(origValue: "Reviews", key: "LBL_REVIEWS").uppercased())"
            providerDetailDesignView.ratingCountLbl.isHidden = false
        }else{
            providerDetailDesignView.ratingCountLbl.isHidden = true
        }
        
        providerDetailDesignView.ratingBar.rating = GeneralFunctions.parseFloat(origValue: 0, data: dataDict.get("average_rating"))
        providerDetailDesignView.providerNameLbl.text = dataDict.get("Name") + " " + dataDict.get("LastName")
        
        if(eUnit == "KMs"){
            providerDetailDesignView.distanceLbl.text = "\(Configurations.convertNumToAppLocal(numStr: String(format: "%.02f", (GeneralFunctions.parseDouble(origValue: 0, data: dataDict.get("DIST_TO_PICKUP")).roundTo(places: 2)))))" + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_KM_DISTANCE_TXT"))"
        }else{
            providerDetailDesignView.distanceLbl.text = "\(Configurations.convertNumToAppLocal(numStr: String(format: "%.02f", (GeneralFunctions.parseDouble(origValue: 0, data: dataDict.get("DIST_TO_PICKUP")) * 0.621371).roundTo(places: 2))))" + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MILE_DISTANCE_TXT"))"
        }
//        if(eUnit == "KMs"){
//            providerDetailDesignView.distanceLbl.text = "\((GeneralFunctions.parseDouble(origValue: 0, data: dataDict.get("DIST_TO_PICKUP")).roundTo(places: 2)))" + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_KM_DISTANCE_TXT"))"
//        }else{
//            providerDetailDesignView.distanceLbl.text = "\(((GeneralFunctions.parseDouble(origValue: 0, data: dataDict.get("DIST_TO_PICKUP")) * 0.621371).roundTo(places: 2)))" + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MILE_DISTANCE_TXT"))"
//        }
        
        providerDetailDesignView.awayLbl.text = "\(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_AWAY").uppercased())"
        providerDetailDesignView.aboutLbl.text = self.generalFunc.getLanguageLabel(origValue: "ABOUT EXPERT", key: "LBL_ABOUT_EXPERT")
        
        providerDetailDesignView.providerImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + "\(dataDict.get("driver_id"))/\(dataDict.get("driver_img"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            
        })
//        providerDetailDesignView.headerImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + "\(dataDict.get("driver_id"))/\(dataDict.get("driver_img"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
//
//        })
        
//        let blurEffectView = UIVisualEffectView(effect: UIBlurEffect(style: UIBlurEffectStyle.dark))
//        blurEffectView.frame = providerDetailDesignView.headerView.bounds
//        blurEffectView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
//        providerDetailDesignView.headerView.addSubview(blurEffectView)
        Utils.createRoundedView(view: providerDetailDesignView.providerImgView, borderColor: UIColor.UCAColor.AppThemeTxtColor, borderWidth: 4)
        
        providerDetailDesignView.providerDetailTxtView.text = dataDict.get("tProfileDescription") == "" ? "----" : dataDict.get("tProfileDescription")
        
    }
    
    func closeView(){
        providerDetailDesignView.frame.origin.y = Application.screenSize.height + 2500
        providerDetailDesignView.removeFromSuperview()
        providerDetailDesignBGView.removeFromSuperview()
    }
}
