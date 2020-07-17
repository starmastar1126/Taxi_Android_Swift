//
//  MinimumAmountReqView.swift
//  DriverApp
//
//  Created by NEW MAC on 24/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class MinimumAmountReqView: UIView {

    @IBOutlet weak var hImgView: UIImageView!
    @IBOutlet weak var hLbl: MyLabel!
    @IBOutlet weak var subLbl: MyLabel!
    @IBOutlet weak var poitiveLbl: MyLabel!
    @IBOutlet weak var negativeLbl: MyLabel!
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
        
        GeneralFunctions.setImgTintColor(imgView: hImgView, color: UIColor.UCAColor.AppThemeColor)
        
    }
    
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "MinimumAmountReqView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }

}
