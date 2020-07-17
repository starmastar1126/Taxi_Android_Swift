//
//  navigationVIew.swift
//  DriverApp
//
//  Created by Tarwinder Singh on 28/12/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class navigationVIew: UIStackView {

    @IBOutlet weak var navImgView: UIImageView!
    @IBOutlet weak var navigateLbl: MyLabel!
    @IBOutlet weak var navContainerView: UIView!
    @IBOutlet weak var tripIntervalLbl: MyLabel!
    @IBOutlet weak var navOptionView: UIView!
    @IBOutlet weak var addressLbl: MyLabel!
    var view: UIView!
    
    override init(frame: CGRect) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    required init(coder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: coder)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    
    func xibSetup() {
        view = loadViewFromNib()
        
        // use bounds not frame or it'll be offset
        view.frame = bounds
        
        addSubview(view)
    }
    
    func loadViewFromNib() -> UIStackView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "navigationVIew", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIStackView
        
        return view
    }

}
