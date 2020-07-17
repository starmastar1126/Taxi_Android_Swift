//
//  OpenAdditionalChargesView.swift
//  DriverApp
//
//  Created by NEW MAC on 09/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

class OpenAdditionalChargesView: NSObject, MyLabelClickDelegate {
    typealias CompletionHandler = (_ isSkipped:Bool, _ materialFee:String, _ miscFee:String, _ providerDiscount:String) -> Void
    
    var uv:ActiveTripUV!
    var containerView:UIView!
    
    var tripData:NSDictionary!
    
    var vPassword = ""
    
    let generalFunc = GeneralFunctions()
    var additionalChargesView:AdditionalChargesView!
    var bgView:UIView!
    var handler:CompletionHandler!
    var iTripTimeId = ""
    var dest_lat = ""
    var dest_lon = ""
    
    let closeImgTapGue = UITapGestureRecognizer()
    
    var fareValue:Double = 0
    var currentMaterialFee:Double = 0
    var currentMiscFee:Double = 0
    var currentProviderDiscount:Double = 0
    
    var currencySymbol = ""
    
    init(uv:ActiveTripUV, containerView:UIView, tripData:NSDictionary, dest_lat:String, dest_lon:String, iTripTimeId:String){
        self.uv = uv
        self.containerView = containerView
        self.tripData = tripData
        self.dest_lat = dest_lat
        self.dest_lon = dest_lon
        self.iTripTimeId = iTripTimeId
        super.init()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(currentFare:String){
        
        if(currentFare == ""){
            getFareData()
            return
        }
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width: self.containerView.frame.width, height: self.containerView.frame.height)
        
        bgView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        let width = (self.containerView.frame.width > 390 ? 375 : (self.containerView.frame.width - 50))
        let extraHeight = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADDITONAL_CHARGE_HINT").height(withConstrainedWidth: width - 30, font: UIFont(name: "Roboto-Medium", size: 17)!)
        
        additionalChargesView = AdditionalChargesView(frame: CGRect(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2, width: width, height: 302 + extraHeight))
        
        additionalChargesView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        Utils.createRoundedView(view: additionalChargesView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        additionalChargesView.layer.shadowOpacity = 0.5
        additionalChargesView.layer.shadowOffset = CGSize(width: 0, height: 3)
        additionalChargesView.layer.shadowColor = UIColor.black.cgColor
        
        additionalChargesView.hLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADDITONAL_CHARGE_HINT")
        additionalChargesView.hLbl.fitText()
        
        additionalChargesView.materialFeeHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MATERIAL_FEE")
        additionalChargesView.currentChargesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CURREANT_HINT")
        additionalChargesView.miscFeeHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MISC_FEE")
        additionalChargesView.providerDiscountHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROVIDER_DISCOUNT")
        
        additionalChargesView.skipLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SKIP_TXT").uppercased()
        additionalChargesView.submitLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SUBMIT_BUTTON_TXT").uppercased()
        
        additionalChargesView.finalTotalHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Final total", key: "LBL_FINAL_TOTAL").uppercased()
        additionalChargesView.finalTotalVLbl.text = currentFare
        additionalChargesView.currentChargesVLbl.text = currentFare
        
        additionalChargesView.materialFeeCurrencyLbl.text = self.currencySymbol
        additionalChargesView.miscFeeCurrencyLbl.text = self.currencySymbol
        additionalChargesView.providerDiscountCurrencyLbl.text = "- \(self.currencySymbol)"
        
        additionalChargesView.skipLbl.setClickDelegate(clickDelegate: self)
        additionalChargesView.submitLbl.setClickDelegate(clickDelegate: self)
        
        self.uv.view.addSubview(bgView)
        self.uv.view.addSubview(additionalChargesView)
        
        closeImgTapGue.addTarget(self, action: #selector(self.closeView))
        additionalChargesView.closeImgView.isUserInteractionEnabled = true
        additionalChargesView.closeImgView.addGestureRecognizer(closeImgTapGue)
        
        additionalChargesView.materialFeeTxtField.setPlaceHolder(placeHolder: "0.00")
        additionalChargesView.miscFeeTxtField.setPlaceHolder(placeHolder: "0.00")
        additionalChargesView.providerDiscountTxtField.setPlaceHolder(placeHolder: "0.00")
        additionalChargesView.materialFeeTxtField.getTextField()!.placeholderAnimation = .hidden
        
        additionalChargesView.miscFeeTxtField.getTextField()!.placeholderAnimation = .hidden
        
        additionalChargesView.providerDiscountTxtField.getTextField()!.placeholderAnimation = .hidden
        
        additionalChargesView.materialFeeTxtField.getTextField()!.addTarget(self, action: #selector(self.textFieldDidChange(textField:)), for: .editingChanged)
        additionalChargesView.miscFeeTxtField.getTextField()!.addTarget(self, action: #selector(self.textFieldDidChange(textField:)), for: .editingChanged)
        additionalChargesView.providerDiscountTxtField.getTextField()!.addTarget(self, action: #selector(self.textFieldDidChange(textField:)), for: .editingChanged)
        
    }
    
    func textFieldDidChange(textField: UITextField) {
        
        if(textField == additionalChargesView.materialFeeTxtField.getTextField()!){
            let currentMaterialFee = GeneralFunctions.parseDouble(origValue: 0.00, data: textField.text!)
            if(self.currentMaterialFee > 0){
                self.fareValue = self.fareValue - self.currentMaterialFee
            }
            self.fareValue = self.fareValue + currentMaterialFee
            
            self.currentMaterialFee = currentMaterialFee
        }
        if(textField == additionalChargesView.miscFeeTxtField.getTextField()!){
            let currentMiscFee = GeneralFunctions.parseDouble(origValue: 0.00, data: textField.text!)
            if(self.currentMiscFee > 0){
                self.fareValue = self.fareValue - self.currentMiscFee
            }
            self.fareValue = self.fareValue + currentMiscFee
            
            self.currentMiscFee = currentMiscFee
        }
        if(textField == additionalChargesView.providerDiscountTxtField.getTextField()!){
            var currentProviderDiscount = GeneralFunctions.parseDouble(origValue: 0.00, data: textField.text!)
            if(self.currentProviderDiscount > 0){
                self.fareValue = self.fareValue + self.currentProviderDiscount
            }
            if((self.fareValue - currentProviderDiscount) < 0){
                currentProviderDiscount = self.fareValue
                additionalChargesView.providerDiscountTxtField.setText(text: "\(String(format: "%.02f", self.fareValue))")
            }
            
            self.fareValue = self.fareValue - currentProviderDiscount
            
            self.currentProviderDiscount = currentProviderDiscount
        }
        
        additionalChargesView.finalTotalVLbl.text = "\(self.currencySymbol)\(String(format: "%.02f", fareValue))"
//        additionalChargesView.currentChargesVLbl.text = "\(self.currencySymbol)\(fareValue)"
    }
    
    func closeView(){
        self.uv.tripTaskExecuted = false
        additionalChargesView.frame.origin.y = Application.screenSize.height + 2500
        additionalChargesView.removeFromSuperview()
        bgView.removeFromSuperview()
        
        self.uv.view.layoutIfNeeded()
    }
    func myLableTapped(sender: MyLabel) {
        if(additionalChargesView != nil){
            if(sender == additionalChargesView.skipLbl){
                if(self.handler != nil){
                    self.handler(true, "", "", "")
                }
            }else if(sender == additionalChargesView.submitLbl){
                if(self.handler != nil){
                    self.handler(false, additionalChargesView.materialFeeTxtField.getTextField()!.text!, additionalChargesView.miscFeeTxtField.getTextField()!.text!, additionalChargesView.providerDiscountTxtField.getTextField()!.text!)
                }
            }
        }
    }
    func getFareData(){
        var parameters = ["type":"displaytripcharges", "TripID": tripData!.get("TripId"),"iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "dest_lat": dest_lat, "dest_lon": dest_lon]
        
        if(tripData.get("eFareType") == "Hourly" && self.iTripTimeId != "" && !self.uv.isResume){
            parameters["iTripTimeId"] = iTripTimeId
        }
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    if(self.tripData.get("eFareType") == "Hourly"){
                        self.uv.stopJobTimer()
                        
                        self.uv.progressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Resume", key: "LBL_RESUME"))
                    }
                    
                    self.fareValue = GeneralFunctions.parseDouble(origValue: 0.0, data: dataDict.get("FareValue"))
                    self.currencySymbol = dataDict.get("CurrencySymbol")
                    
                    self.show(currentFare: dataDict.get(Utils.message_str))
                    
//                    let window = Application.window
//                    
//                    let getUserData = GetUserData(uv: self, window: window!)
//                    getUserData.getdata()
                    
                }else{
                    self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self.uv)
            }
        })
        
    }
}
