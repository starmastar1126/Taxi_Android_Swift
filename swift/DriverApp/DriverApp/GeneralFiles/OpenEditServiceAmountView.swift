//
//  OpenEditServiceAmountView.swift
//  DriverApp
//
//  Created by NEW MAC on 03/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenEditServiceAmountView: NSObject, MyLabelClickDelegate {
    typealias CompletionHandler = (_ isNegativeBtnClicked:Bool, _ isPositiveBtnClicked:Bool, _ view:UIView, _ bgView:UIView) -> Void
    
    var uv:UIViewController!
    var containerView:UIView!
    
    var currentInst:OpenMinAmountReqView!
    
    let generalFunc = GeneralFunctions()
    var bgView:UIView!
    
    var editServiceAmountView:EditServiceAmountView!
    
    var handler:CompletionHandler!
    
    var amount_str = ""
    
    var currencySymbol = ""
    
    init(uv:UIViewController, containerView:UIView){
        self.uv = uv
        self.containerView = containerView
        super.init()
    }
    
    func setHandler(handler:@escaping CompletionHandler){
        self.handler = handler
    }
    
    func show(msg:String, currentAmount:String, currencySymbol:String){
        self.currencySymbol = currencySymbol
        
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width: self.containerView.frame.width, height: self.containerView.frame.height)
        
        bgView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        let width = (self.containerView.frame.width > 390 ? 375 : (self.containerView.frame.width - 50))
        let extraHeight = msg.height(withConstrainedWidth: width - 30, font: UIFont(name: "Roboto-Light", size: 18)!) - 20
        
        editServiceAmountView = EditServiceAmountView(frame: CGRect(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2, width: width, height: 250 + extraHeight))
        
        editServiceAmountView.center = CGPoint(x: self.containerView.frame.width / 2, y: self.containerView.frame.height / 2)
        
        editServiceAmountView.msgLbl.text = msg
        editServiceAmountView.msgLbl.fitText()
        
        editServiceAmountView.positiveLbl.text = self.generalFunc.getLanguageLabel(origValue: "Confirm", key: "LBL_CONFIRM_TXT")
        editServiceAmountView.negativeLbl.text = self.generalFunc.getLanguageLabel(origValue: "Cancel", key: "LBL_CANCEL_TXT")
        
        editServiceAmountView.positiveLbl.setClickDelegate(clickDelegate: self)
        editServiceAmountView.negativeLbl.setClickDelegate(clickDelegate: self)
        
        Utils.createRoundedView(view: editServiceAmountView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        editServiceAmountView.layer.shadowOpacity = 0.5
        editServiceAmountView.layer.shadowOffset = CGSize(width: 0, height: 3)
        editServiceAmountView.layer.shadowColor = UIColor.black.cgColor
        
        editServiceAmountView.amountTxtField.maxCharacterLimit = 12
        editServiceAmountView.amountTxtField.nonDeletePrefixStr = currencySymbol
        editServiceAmountView.amountTxtField.setText(text: "\(currencySymbol)\(currentAmount)")
        editServiceAmountView.amountTxtField.getTextField()!.placeholderAnimation = .hidden
        editServiceAmountView.amountTxtField.getTextField()!.keyboardType = .decimalPad
        editServiceAmountView.amountTxtField.defaultTextAlignment = .center
        
        //        let currentWindow = Application.window
        
        //        if(currentWindow != nil){
        //            currentWindow?.addSubview(bgView)
        //            currentWindow?.addSubview(enableLocationView)
        //            currentWindow?.addSubview(navBar)
        //        }else{
        self.uv.view.addSubview(bgView)
        self.uv.view.addSubview(editServiceAmountView)
        //            currentWindow?.addSubview(navBar)
        //        }
        
    }
    
    func closeView(){
        editServiceAmountView.frame.origin.y = Application.screenSize.height + 2500
        editServiceAmountView.removeFromSuperview()
        bgView.removeFromSuperview()
        
        self.uv.view.layoutIfNeeded()
    }
    
    func myLableTapped(sender: MyLabel) {
        if(editServiceAmountView != nil){
            if(sender == editServiceAmountView.positiveLbl){
                
                let required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
                let error_money = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_CORRECT_DETAIL_TXT")
                
                let amount_str = Configurations.convertNumToEnglish(numStr: Utils.getText(textField: self.editServiceAmountView.amountTxtField.getTextField()!).replace(currencySymbol, withString: ""))
                
                let moneyEntered = (amount_str != "") ? ((amount_str != "0" && amount_str.isNumeric() && (GeneralFunctions.parseDouble(origValue: 0.00, data: amount_str) > 0)) ? true: Utils.setErrorFields(textField: self.editServiceAmountView.amountTxtField.getTextField()!, error: error_money)) : Utils.setErrorFields(textField: self.editServiceAmountView.amountTxtField.getTextField()!, error: required_str)
                
                if(moneyEntered){
                    
                    self.amount_str = amount_str
                    
                    if(handler != nil){
                        handler(false, true, editServiceAmountView, bgView)
                    }
                    
                    self.closeView()
                }
                
            }else if(sender == editServiceAmountView.negativeLbl){
                
                if(handler != nil){
                    handler(true, false, editServiceAmountView, bgView)
                }
                
                self.closeView()
            }
        }
    }
}
