//
//  UIColorExt.swift
//  DriverApp
//
//  Created by NEW MAC on 04/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
extension UIColor {
    convenience public init(hex: Int, alpha: CGFloat = 1.0) {
        let red = CGFloat((hex & 0xFF0000) >> 16) / 255.0
        let green = CGFloat((hex & 0xFF00) >> 8) / 255.0
        let blue = CGFloat((hex & 0xFF)) / 255.0
        self.init(red:red, green:green, blue:blue, alpha:alpha)
    }
    
    convenience init(colorWithHexValue value: Int, alpha:CGFloat = 1.0){
        self.init(
            red: CGFloat((value & 0xFF0000) >> 16) / 255.0,
            green: CGFloat((value & 0x00FF00) >> 8) / 255.0,
            blue: CGFloat(value & 0x0000FF) / 255.0,
            alpha: alpha
        )
    }
    
    public struct UCAColor {
        public static let Red = UIColor(hex: 0xF44336)
        public static let Pink = UIColor(hex: 0xE91E63)
        public static let Purple = UIColor(hex: 0x9C27B0)
        public static let DeepPurple = UIColor(hex: 0x673AB7)
        public static let Indigo = UIColor(hex: 0x3F51B5)
        public static let Blue = UIColor(hex: 0x2196F3)
        public static let LightBlue = UIColor(hex: 0x03A9F4)
        public static let Cyan = UIColor(hex: 0x00BCD4)
        public static let Teal = UIColor(hex: 0x009688)
        public static let Green = UIColor(hex: 0x4CAF50)
        public static let LightGreen = UIColor(hex: 0x8BC34A)
        public static let Lime = UIColor(hex: 0xCDDC39)
        public static let Yellow = UIColor(hex: 0xFFEB3B)
        public static let Amber = UIColor(hex: 0xFFC107)
        public static let Orange = UIColor(hex: 0xFF9800)
        public static let DeepOrange = UIColor(hex: 0xFF5722)
        public static let Brown = UIColor(hex: 0x795548)
        public static let Grey = UIColor(hex: 0x9E9E9E)
        public static let BlueGrey = UIColor(hex: 0x607D8B)
        public static let blackColor = UIColor(hex: 0x000000)
        public static let status_blue_border = UIColor(hex: 0x1CC9E8)
        
        public static let AppThemeColor = UIColor(hex: 0xffa524)
        public static let AppThemeColor_Dark = UIColor(hex: 0xcc841c)
        public static let AppThemeColor_Hover = UIColor(hex: 0xcc841c)
        public static let AppThemeTxtColor = UIColor(hex: 0xFFFFFF)
        
        public static let AppThemeColor_1 = UIColor(hex: 0x2c3032)
        public static let AppThemeColor_1_Hover = UIColor(hex: 0xcc841c)
        public static let AppThemeTxtColor_1 = UIColor(hex: 0xFFFFFF)
        
        public static let textFieldActiveColor = AppThemeColor
        public static let textFieldDividerInActiveColor = UIColor(hex: 0xbfbfbf)
        public static let textFieldDividerActiveColor = AppThemeColor
        public static let textFieldPlaceholderInActiveColor = UIColor(hex: 0x999999)
        public static let textFieldPlaceholderActiveColor = AppThemeColor
        
        
        public static let buttonBgColor = AppThemeColor_1
        public static let buttonTextColor = AppThemeTxtColor_1
        
        
        public static let menuListBg = UIColor(hex: 0xFFFFFF)
        
        public static let menuListTxtColor = UIColor(hex: 0x454545)
        
    }
    
    var hexString: String {
        let components = self.cgColor.components
        
        let red = Float(components![0])
        let green = Float(components![1])
        let blue = Float(components![2])
        return String(format: "#%02lX%02lX%02lX", lroundf(red * 255), lroundf(green * 255), lroundf(blue * 255))
    }
    
    func lighter(by percentage:CGFloat=20.0) -> UIColor? {
        return self.adjust(by: abs(percentage) )
    }
    
    func darker(by percentage:CGFloat=20.0) -> UIColor? {
        return self.adjust(by: -1 * abs(percentage) )
    }
    
    func adjust(by percentage:CGFloat=20.0) -> UIColor? {
        var r:CGFloat=0, g:CGFloat=0, b:CGFloat=0, a:CGFloat=0;
        if(self.getRed(&r, green: &g, blue: &b, alpha: &a)){
            return UIColor(red: min(r + percentage/100, 1.0),
                           green: min(g + percentage/100, 1.0),
                           blue: min(b + percentage/100, 1.0),
                           alpha: a)
        }else{
            return nil
        }
    }
}
