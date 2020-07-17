//
//  NavigationOptionView.swift
//  DriverApp
//
//  Created by NEW MAC on 22/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class NavigationOptionView: UIView {
    
    typealias CompletionHandler = (_ view:UIView, _ optionId:Int) -> Void

    @IBOutlet weak var headerImgView: UIImageView!
    @IBOutlet weak var headerLbl: MyLabel!
    @IBOutlet weak var googleNavView: UIView!
    @IBOutlet weak var wazeNavView: UIView!
    @IBOutlet weak var googleNavLbl: MyLabel!
    @IBOutlet weak var wazeNavLbl: MyLabel!
    var view: UIView!
    
    let generalFunc = GeneralFunctions()
    
    var handler:CompletionHandler!
    
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
        
        self.googleNavLbl.text = self.generalFunc.getLanguageLabel(origValue: "Google map navigation", key: "LBL_NAVIGATION_GOOGLE_MAP")
        self.wazeNavLbl.text = self.generalFunc.getLanguageLabel(origValue: "Waze navigation", key: "LBL_NAVIGATION_WAZE")
        self.headerLbl.text = self.generalFunc.getLanguageLabel(origValue: "Choose Option", key: "LBL_CHOOSE_OPTION")
        
        let googleTapGue = UITapGestureRecognizer()
        let wazeTapGue = UITapGestureRecognizer()
        
        googleTapGue.addTarget(self, action: #selector(self.googleTapped))
        wazeTapGue.addTarget(self, action: #selector(self.wazeTapped))
        
        googleNavView.isUserInteractionEnabled = true
        wazeNavLbl.isUserInteractionEnabled = true
        
        googleNavView.addGestureRecognizer(googleTapGue)
        wazeNavView.addGestureRecognizer(wazeTapGue)
        
        GeneralFunctions.setImgTintColor(imgView: headerImgView, color: UIColor.UCAColor.AppThemeColor)
    }
    
    func setHandler(handler:@escaping CompletionHandler){
        self.handler = handler
    }
    
    
    func googleTapped(){
        if(handler != nil){
            handler(view, 0)
        }
    }
    
    func wazeTapped(){
        if(handler != nil){
            handler(view, 1)
        }
    }
    
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "NavigationOptionView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }

}
