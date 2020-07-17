//
//  ExtCALayer.swift
//  DriverApp
//
//  Created by NEW MAC on 26/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//
import Foundation

extension CALayer {
    
    func addBorder(edge: UIRectEdge, color: UIColor, thickness: CGFloat) {
        
        let border = CALayer()
        
        switch edge {
        case UIRectEdge.top:
            border.frame = CGRect(x: 0, y: 0, width: self.frame.width, height: thickness)
            break
        case UIRectEdge.bottom:
            border.frame = CGRect(x: 0, y: self.frame.height - thickness, width: self.frame.width, height: thickness)
            break
        case UIRectEdge.left:
            border.frame = CGRect(x: 0, y: 0, width: thickness, height: self.frame.height)
            break
        case UIRectEdge.right:
            border.frame = CGRect(x: self.frame.width - thickness, y: 0, width: thickness, height: self.frame.height)
            break
        default:
            break
        }
        
        border.backgroundColor = color.cgColor;
        
        self.addSublayer(border)
    }
    
    func addDashedBorder(edge: UIRectEdge, color: UIColor = .lightGray, thickness:CGFloat) {
        self.sublayers?.filter({ $0.name == "DashedTopLine" }).forEach({ $0.removeFromSuperlayer() })
        backgroundColor = UIColor.clear.cgColor
        
        let shapeLayer = CAShapeLayer()
        shapeLayer.name = "DashedTopLine"
        shapeLayer.fillColor = UIColor.clear.cgColor
        shapeLayer.strokeColor = color.cgColor
        shapeLayer.lineWidth = thickness
        shapeLayer.lineJoin = kCALineJoinRound
        shapeLayer.lineDashPattern = [4, 4]
        shapeLayer.bounds = bounds
        
        switch edge {
        case UIRectEdge.top:
            
            let path = CGMutablePath()
            path.move(to: CGPoint(x:0, y: 0))
            path.addLine(to: CGPoint(x: frame.width, y: 0))
            shapeLayer.path = path
            
            self.addSublayer(shapeLayer)
            
            shapeLayer.frame = CGRect(x: 0, y: 0 - thickness - 3, width: self.frame.width, height: thickness)
            break
        case UIRectEdge.bottom:
            
            let path = CGMutablePath()
            path.move(to: CGPoint(x:0, y: 0))
            path.addLine(to: CGPoint(x: frame.width, y: 0))
            shapeLayer.path = path
            
            self.addSublayer(shapeLayer)
            shapeLayer.frame = CGRect(x: 0, y: self.frame.height + thickness + 3, width: self.frame.width, height: thickness)
            break
        case UIRectEdge.left:
            
            let path = CGMutablePath()
            path.move(to: CGPoint(x:0, y: 0))
            path.addLine(to: CGPoint(x: 0, y: frame.height))
            shapeLayer.path = path
            
            self.addSublayer(shapeLayer)
            
            shapeLayer.frame = CGRect(x: 0 - thickness - 3, y: 0 , width: thickness, height: frame.height)
            
            break
        case UIRectEdge.right:
            let path = CGMutablePath()
            path.move(to: CGPoint(x:0, y: 0))
            path.addLine(to: CGPoint(x: 0, y: frame.height))
            shapeLayer.path = path
            
            self.addSublayer(shapeLayer)
            
            shapeLayer.frame = CGRect(x: frame.width + thickness + 3, y: 0 , width: thickness, height: frame.height)
            break
        default:
            break
        }
        
    }
    
}
