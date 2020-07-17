//
//  CancelBookingView.swift
//  PassengerApp
//
//  Created by NEW MAC on 06/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class CancelBookingView: UIView, MyLabelClickDelegate {

    typealias CompletionHandler = (_ isViewRemoved:Bool, _ view:UIView, _ isPositiveBtnClicked:Bool, _ reason:String) -> Void
    
    @IBOutlet weak var cancelBookingHLbl: MyLabel!
    @IBOutlet weak var enterReason: MyTextField!
    @IBOutlet weak var positiveLbl: MyLabel!
    @IBOutlet weak var negativeLbl: MyLabel!
    
    
    var view: UIView!
    var handler:CompletionHandler!
    
    let generalFunc = GeneralFunctions()
    
    var bookingType = ""
    var PAGE_TYPE = ""
    
    override init(frame: CGRect) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    required init?(coder aDecoder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: aDecoder)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func xibSetup() {
        view = loadViewFromNib()
        
        // use bounds not frame or it'll be offset
        view.frame = bounds
        
        // Make the view stretch with containing view
        //        view.autoresizingMask = [UIViewAutoresizing.flexibleWidth, UIViewAutoresizing.flexibleHeight]
        // Adding custom subview on top of our view (over any custom drawing > see note below)
        addSubview(view)
        
        setLabels()
        
        self.positiveLbl.setClickDelegate(clickDelegate: self)
        self.negativeLbl.setClickDelegate(clickDelegate: self)
    }
    
    func setLabels(){
        
        self.enterReason.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ENTER_REASON"))
        self.positiveLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT")
        self.negativeLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT")
        
        if(self.bookingType != Utils.cabGeneralType_UberX || self.PAGE_TYPE.uppercased() != "PENDING"){
            self.cancelBookingHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_BOOKING")
        }else{
            self.cancelBookingHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Decline Booking", key: "LBL_DECLINE_BOOKING")
        }
    }
    
    func myLableTapped(sender: MyLabel) {
        
        
        if(sender == positiveLbl){
            let reasonEntered = Utils.checkText(textField: self.enterReason.getTextField()!) ? true : Utils.setErrorFields(textField: self.enterReason.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT"))
            if(reasonEntered == true && handler != nil){
                self.handler(true, view, true, Utils.getText(textField: self.enterReason.getTextField()!))
            }else{
                return
            }
            
        }else if(sender == negativeLbl){
            if(handler != nil){
                self.handler(true, view, false, Utils.getText(textField: self.enterReason.getTextField()!))
            }
        }
        
        self.view.frame = CGRect(x:0,y:0, width:0,height:0)
        self.view.isHidden = true
        self.view.removeFromSuperview()
    }
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "CancelBookingView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }

}
