//
//  CustomNavBar.swift
//  DriverApp
//
//  Created by NEW MAC on 20/11/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class CustomNavBar: UINavigationBar {
    
    //set NavigationBar's height
    //    var customHeight : CGFloat = 66
    @IBInspectable open var customHeight: CGFloat = 64
    
    override func sizeThatFits(_ size: CGSize) -> CGSize {
        
        return CGSize(width: UIScreen.main.bounds.width, height: customHeight)
        
    }
    
    override func layoutSubviews() {
        super.layoutSubviews()
        
        frame = CGRect(x: frame.origin.x, y:  0, width: frame.size.width, height: customHeight)
        
        // title position (statusbar height / 2)
        setTitleVerticalPositionAdjustment(-10, for: UIBarMetrics.default)
        
        for subview in self.subviews {
            var stringFromClass = NSStringFromClass(subview.classForCoder)
            if stringFromClass.contains("BarBackground") {
                subview.frame = CGRect(x: 0, y: 0, width: self.frame.width, height: customHeight)
                
            }
            
            stringFromClass = NSStringFromClass(subview.classForCoder)
            if stringFromClass.contains("BarContent") {
                
                subview.frame = CGRect(x: subview.frame.origin.x, y: 20, width: subview.frame.width, height: customHeight - 20)
                
            }
        }
    }
    
    func changeHeight(customHeight:CGFloat){
        self.customHeight = customHeight
        
        self.layoutSubviews()
    }
    
}

