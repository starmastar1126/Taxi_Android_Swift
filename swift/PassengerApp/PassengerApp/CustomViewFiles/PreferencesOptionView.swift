//
//  PreferencesOptionView.swift
//  PassengerApp
//
//  Created by NEW MAC on 27/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class PreferencesOptionView: UIView {
    
    
    @IBOutlet weak var prefHLbl: MyLabel!
    @IBOutlet weak var genderView: UIView!
    @IBOutlet weak var genderLbl: MyLabel!
    @IBOutlet weak var genderChkBox: BEMCheckBox!
    @IBOutlet weak var genderViewHeight: NSLayoutConstraint!
    @IBOutlet weak var handiCapView: UIView!
    @IBOutlet weak var handiCapChkBox: BEMCheckBox!
    @IBOutlet weak var handiCapViewHeight: NSLayoutConstraint!
    @IBOutlet weak var handiCaplbl: MyLabel!
    @IBOutlet weak var setPrefBtn: MyButton!
    
    
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
        
        self.genderChkBox.boxType = .square
        self.genderChkBox.offAnimationType = .bounce
        self.genderChkBox.onAnimationType = .bounce
        self.genderChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
        self.genderChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
        self.genderChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
        self.genderChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
        
        
        self.handiCapChkBox.boxType = .square
        self.handiCapChkBox.offAnimationType = .bounce
        self.handiCapChkBox.onAnimationType = .bounce
        self.handiCapChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
        self.handiCapChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
        self.handiCapChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
        self.handiCapChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
        
        
    }
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "PreferencesOptionView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }
}
