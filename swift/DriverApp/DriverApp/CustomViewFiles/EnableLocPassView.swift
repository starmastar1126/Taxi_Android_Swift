//
//  EnableLocationView.swift
//  PassengerApp
//
//  Created by NEW MAC on 28/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class EnableLocPassView: UIView {
    
    @IBOutlet weak var iconImgView: UIImageView!
    @IBOutlet weak var headerLbl: MyLabel!
    @IBOutlet weak var contentLbl: MyLabel!
    @IBOutlet weak var turnOnBtn: MyButton!
    @IBOutlet weak var enterPickUpBtn: MyButton!
    
    var view: UIView!
    
    let generalFunc = GeneralFunctions()
    
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
    
    
    func xibSetup() {
        view = loadViewFromNib()
        
        // use bounds not frame or it'll be offset
        view.frame = bounds
        
        // Make the view stretch with containing view
        //        view.autoresizingMask = [UIViewAutoresizing.flexibleWidth, UIViewAutoresizing.flexibleHeight]
        // Adding custom subview on top of our view (over any custom drawing > see note below)
        addSubview(view)
        
        if(InternetConnection.isConnectedToNetwork() == false)
        {
            headerLbl.text = self.generalFunc.getLanguageLabel(origValue: "Internet Connection", key: "LBL_NO_INTERNET_TITLE")
            headerLbl.fitText()
            
            contentLbl.text = self.generalFunc.getLanguageLabel(origValue: "Application requires internet connection to be enabled. Please check your network settings.", key: "LBL_NO_INTERNET_SUB_TITLE")
            contentLbl.fitText()
            
            turnOnBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Settings", key: "LBL_SETTINGS"))
            
            enterPickUpBtn.isHidden = true
            //enterPickUpBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "ENTER PICK UP ADDRESS", key: "LBL_ENTER_PICK_UP_ADDRESS"))
        }
        else
        {
            enterPickUpBtn.isHidden = false
            headerLbl.text = self.generalFunc.getLanguageLabel(origValue: "Location services turned off", key: "LBL_LOCATION_SERVICES_TURNED_OFF")
            headerLbl.fitText()
            
            contentLbl.text = self.generalFunc.getLanguageLabel(origValue: "Turn on location services in your device settings to improve your pickup experience.", key: "LBL_LOCATION_SERVICES_TURNED_OFF_DETAILS")
            contentLbl.fitText()
            
            turnOnBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "TURN ON LOCATION SERVICES", key: "LBL_TURN_ON_LOC_SERVICE"))
            enterPickUpBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "ENTER PICK UP ADDRESS", key: "LBL_ENTER_PICK_UP_ADDRESS"))
        }
    }
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "EnableLocPassView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }
}

