//
//  PaddingLabel.swift
//  Login_SignUp
//
//  Created by Chirag on 09/12/15.
//  Copyright © 2015 ESW. All rights reserved.
//

import UIKit

protocol MyLabelClickDelegate {
    func myLableTapped(sender:MyLabel)
}

class MyLabel: UILabel {

    @IBInspectable internal var paddingRight:CGFloat = 0
    @IBInspectable internal var paddingLeft:CGFloat = 0
    @IBInspectable internal var paddingTop:CGFloat = 0
    @IBInspectable internal var paddingBottom:CGFloat = 0
    
    @IBInspectable internal var fontFamilyName:String!
    
    @IBInspectable internal var isAppThemeBg:Bool = false
    @IBInspectable internal var isAppTheme1Bg:Bool = false
    @IBInspectable internal var isAppThemeFont:Bool = false
    @IBInspectable internal var isAppTheme1Font:Bool = false
    @IBInspectable internal var isAppThemeTextFont:Bool = false
    @IBInspectable internal var isAppThemeText1Font:Bool = false
    
    var xOffset:CGFloat = 0
    var yOffset:CGFloat = 0
    
    var isInCenterOfScreen = false
    
    // MARK:- Delegate
    var clickDelegate:MyLabelClickDelegate?
    
    let tapGue = UITapGestureRecognizer()
    
    override init(frame: CGRect) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        setConfig()
    }
    
    required init?(coder aDecoder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: aDecoder)
        
        setConfig()
    }
    
    func setConfig(){
        setFontFamily()
        setColor()

    }
    
    func setFontFamily(){
        if(fontFamilyName == nil){
            fontFamilyName = "Roboto-Light"
        }
        self.font = UIFont(name: fontFamilyName!, size: self.font.pointSize)
    }
    
    func setColor(){
        if(isAppThemeBg == true){
            self.backgroundColor = UIColor.UCAColor.AppThemeColor
        }else if(isAppTheme1Bg == true){
            self.backgroundColor = UIColor.UCAColor.AppThemeColor_1
        }
        
        if(isAppThemeFont == true){
            self.textColor = UIColor.UCAColor.AppThemeColor
        }else if(isAppTheme1Font == true){
            self.textColor = UIColor.UCAColor.AppThemeColor_1
        }else if(isAppThemeTextFont == true){
            self.textColor = UIColor.UCAColor.AppThemeTxtColor
        }else if(isAppThemeText1Font == true){
            self.textColor = UIColor.UCAColor.AppThemeTxtColor_1
        }
    }
    
    func setClickDelegate(clickDelegate:MyLabelClickDelegate){
        tapGue.addTarget(self, action: #selector(self.myLblTapped(sender:)))
        self.isUserInteractionEnabled = true
        self.addGestureRecognizer(tapGue)
        
        self.clickDelegate = clickDelegate
    }
    
    func myLblTapped(sender:UITapGestureRecognizer){
        clickDelegate?.myLableTapped(sender: self)
    }
    
    override func drawText(in rect: CGRect) {
        setFontFamily()
        
        setColor()
        if(isInCenterOfScreen){
            self.center = self.superview != nil ? self.superview!.center : (CGPoint((Application.screenSize.width  / 2) - (self.frame.width / 2),
                                                                                    (Application.screenSize.height / 2)  - (self.frame.height / 2)))
            
            self.center.x = self.center.x + xOffset
            self.center.y = self.center.y + yOffset
            
        }
        
        super.drawText(in: UIEdgeInsetsInsetRect(rect, getPadding()))
        
        invalidateIntrinsicContentSize()
    }
    
    func getPadding() -> UIEdgeInsets{
        let padding = UIEdgeInsets(top: paddingTop, left: paddingLeft, bottom: paddingBottom, right: paddingRight)

        return padding
    }
    
    func setPadding(paddingTop:CGFloat, paddingBottom:CGFloat, paddingLeft:CGFloat, paddingRight:CGFloat){
        self.paddingTop = paddingTop
        self.paddingBottom = paddingBottom
        self.paddingLeft = paddingLeft
        self.paddingRight = paddingRight
        
        invalidateIntrinsicContentSize()
    }
    
    func setInCenter(isInCenterOfScreen:Bool){
        self.isInCenterOfScreen = isInCenterOfScreen
    }
    
    // Override -intrinsicContentSize: for Auto layout code
    override public var intrinsicContentSize: CGSize {
        let superContentSize = super.intrinsicContentSize
        let width = superContentSize.width + getPadding().left + getPadding().right
        let heigth = superContentSize.height + getPadding().top + getPadding().bottom
        return CGSize(width: width, height: heigth)
    }
    
    
    // Override -sizeThatFits: for Springs & Struts code
    override func sizeThatFits(_ size: CGSize) -> CGSize {
        let superSizeThatFits = super.sizeThatFits(size)
        let width = superSizeThatFits.width + getPadding().left + getPadding().right
        let heigth = superSizeThatFits.height + getPadding().top + getPadding().bottom
        return CGSize(width: width, height: heigth)
    }

    
}
