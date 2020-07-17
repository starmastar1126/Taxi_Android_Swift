//
//  AdditionalChargesView.swift
//  DriverApp
//
//  Created by NEW MAC on 09/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AdditionalChargesView: UIView {

    @IBOutlet weak var hLbl: MyLabel!
    @IBOutlet weak var currentChargesHLbl: MyLabel!
    @IBOutlet weak var currentChargesVLbl: MyLabel!
    @IBOutlet weak var materialFeeHLbl: MyLabel!
    @IBOutlet weak var materialFeeCurrencyLbl: MyLabel!
    @IBOutlet weak var materialFeeTxtField: MyTextField!
    
    @IBOutlet weak var miscFeeHLbl: MyLabel!
    @IBOutlet weak var miscFeeCurrencyLbl: MyLabel!
    @IBOutlet weak var miscFeeTxtField: MyTextField!
    
    @IBOutlet weak var providerDiscountHLbl: MyLabel!
    @IBOutlet weak var providerDiscountCurrencyLbl: MyLabel!
    @IBOutlet weak var providerDiscountTxtField: MyTextField!
    @IBOutlet weak var finalTotalHLbl: MyLabel!
    @IBOutlet weak var finalTotalVLbl: MyLabel!
    @IBOutlet weak var submitLbl: MyLabel!
    @IBOutlet weak var skipLbl: MyLabel!
    @IBOutlet weak var closeImgView: UIImageView!
    
    var view: UIView!
    
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
        materialFeeTxtField.getTextField()!.keyboardType = .decimalPad
        miscFeeTxtField.getTextField()!.keyboardType = .decimalPad
        providerDiscountTxtField.getTextField()!.keyboardType = .decimalPad
        
        GeneralFunctions.setImgTintColor(imgView: closeImgView, color: UIColor.UCAColor.AppThemeColor)
    }
    
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "AdditionalChargesView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }

   
}
