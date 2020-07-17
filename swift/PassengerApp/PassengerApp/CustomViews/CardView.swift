//
//  CardView.swift
//  UberCloneApp
//
//  Created by Chirag on 14/12/15.
//  Copyright © 2015 ESW. All rights reserved.
//

import UIKit

class CardView: UIView {

//    var radius: CGFloat = 1
    @IBInspectable internal var radius:CGFloat = 1
    @IBInspectable internal var shadowOpticity:CGFloat = 0.5
    
    override func layoutSubviews() {
        layer.cornerRadius = radius
        let shadowPath = UIBezierPath(roundedRect: bounds, cornerRadius: radius)
        
        layer.masksToBounds = false
        layer.shadowColor = UIColor.black.cgColor
        layer.shadowOffset = CGSize(width: 0, height: 1);
        layer.shadowOpacity = 0.5
        layer.shadowPath = shadowPath.cgPath
    }
    
    required init?(coder aDecoder: NSCoder) {
        super.init(coder: aDecoder)
    }

}
