//
//  RaisedBtnExt.swift
//  DriverApp
//
//  Created by NEW MAC on 11/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
extension RaisedButton {
    
    func setAppThemeColor(_ sender: UIButton) {
        sender.backgroundColor = UIColor.UCAColor.buttonBgColor
        
        DispatchQueue.main.async {
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: UIControlState())
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .highlighted)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .application)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .reserved)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .selected)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .disabled)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .focused)
        }
        
    }
    
    func setAppThemeColor(_ sender: UIButton, isAppTheme:Bool) {
        sender.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        DispatchQueue.main.async {
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: UIControlState())
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: .highlighted)
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: .application)
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: .reserved)
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: .selected)
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: .disabled)
            sender.setTitleColor(UIColor.UCAColor.AppThemeTxtColor, for: .focused)
        }
        
    }
    
    func setAppThemeColor(_ sender: UIButton,disabledColor:UIColor) {
        sender.backgroundColor = UIColor.UCAColor.buttonBgColor
        
        DispatchQueue.main.async {
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: UIControlState())
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .highlighted)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .application)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .reserved)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .selected)
            sender.setTitleColor(disabledColor, for: .disabled)
            sender.setTitleColor(UIColor.UCAColor.buttonTextColor, for: .focused)
        }
        
    }
    
    func setBtnTxtColor(_ color:UIColor) {
        DispatchQueue.main.async {
            self.setTitleColor(color, for: UIControlState())
            self.setTitleColor(color, for: .highlighted)
            self.setTitleColor(color, for: .application)
            self.setTitleColor(color, for: .reserved)
            self.setTitleColor(color, for: .selected)
            self.setTitleColor(color, for: .disabled)
            self.setTitleColor(color, for: .focused)
        }
        
    }
}
