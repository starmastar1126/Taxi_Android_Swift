//
//  TriangleView.swift
//  DriverApp
//
//  Created by NEW MAC on 27/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class TriangleView: UIView {

    
    @IBInspectable open var isAppTheme: Bool = false
    
    @IBInspectable open var isAppTheme1: Bool = true
    
    
    // Only override draw() if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    override func draw(_ rect: CGRect) {
        
        let context = UIGraphicsGetCurrentContext()
        
        // Paint the View Blue before drawing the traingle
        context!.setFillColor(isAppTheme ? UIColor.UCAColor.AppThemeTxtColor.cgColor : (isAppTheme1 ? UIColor.UCAColor.AppThemeTxtColor_1.cgColor : UIColor.blue.cgColor))  // Set fill color
        context!.fill(rect) // Fill rectangle using the context data
        
        // Imagine a triangle resting on the bottom of the container with the base as the width of the rectangle, and the apex of the traingle at the top center of the container
        // The co-ordinates of the rectangle will look like
        // Top = (x: half of Container Width, y: 0 - origin)
        // Bottom Left = (x: 0, y: Container Height)
        // Bottom Right = (x: Container Width, y: Container Height)
        
        // Create path for drawing a triangle
        let trianglePath = UIBezierPath()
        // First move to the Top point
        trianglePath.move(to: CGPoint(x: 0.0, y: self.bounds.height/2))
        // Add line to Bottom Right
        trianglePath.addLine(to: CGPoint(x: self.bounds.width, y: self.bounds.height))
        // Add line to Bottom Left
        trianglePath.addLine(to: CGPoint(x: self.bounds.width, y: 0.0))
        // Complete path by drawing path to the Top
        trianglePath.addLine(to: CGPoint(x: 0.0, y: self.bounds.height/2))
        
        // Set the fill color
        context!.setFillColor(isAppTheme ? UIColor.UCAColor.AppThemeColor.cgColor : (isAppTheme1 ? UIColor.UCAColor.AppThemeColor_1.cgColor : UIColor.green.cgColor))
        // Fill the triangle path
        trianglePath.fill()
        
        context!.flush()
        
        
        if(Configurations.isRTLMode()){
            var scalingTransform : CGAffineTransform!
            scalingTransform = CGAffineTransform(scaleX: -1, y: 1);
            self.transform = scalingTransform
        }
    }
    

}
