//
//  SDragView.swift
//  SDragView
//
//  Created by Admin on 9/20/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class SDragView: NSObject {
    
    
    typealias ViewHandler = (_ view:UIView, _ frame:CGRect) -> Void
    typealias ViewStateHandler = (_ isViewElapsed:Bool, _ isViewCollapsed:Bool,_ isViewDraging:Bool, _ isViewDragingEnd:Bool, _ frame:CGRect) -> Void
    
    //  MARK: - Public properties
    public var viewCornerRadius:CGFloat = 8
    public var viewBackgroundColor:UIColor = UIColor.white
    
    
    // MARK: - Private properties
    private var dragViewAnimatedTopMargin:CGFloat = 25.0 // View fully visible (upper spacing)
    private var viewDefaultHeight:CGFloat = 80.0// View height when appear
    private var gestureRecognizer = UIPanGestureRecognizer()
    private var dragViewDefaultTopMargin:CGFloat!
    private var viewLastYPosition = 0.0
    
    var handler:ViewStateHandler!
    var viewHandler:ViewHandler!
    
    var view:UIView!
    
//    override init(frame: CGRect) {
//        // 1. setup any properties here
//        
//        // 2. call super.init(frame:)
//        super.init(frame: frame)
//        
//    }
//    
//    required init?(coder aDecoder: NSCoder) {
//        // 1. setup any properties here
//        
//        // 2. call super.init(coder:)
//        super.init(coder: aDecoder)
//        
//    }
    
//    required init(dragViewAnimatedTopSpace:CGFloat, viewDefaultHeightConstant:CGFloat, handler: @escaping ViewStateHandler)
//    {
//        
//        initializeDragView()
//        
//    }
//    
//    required init?(coder aDecoder: NSCoder) {
//        fatalError("init(coder:) has not been implemented")
//        
//        initializeDragView()
//    }
    
    func setViewHandler(viewHandler:@escaping ViewHandler){
        self.viewHandler = viewHandler
    }
    
    func setHandler(handler:@escaping ViewStateHandler){
        self.handler = handler
    }
    
    func initializeDragView(view:UIView, dragViewAnimatedTopSpace:CGFloat, viewDefaultHeightConstant:CGFloat, containerView:UIView){
        
        
        dragViewAnimatedTopMargin = dragViewAnimatedTopSpace
        viewDefaultHeight = viewDefaultHeightConstant
        
//        let screenSize: CGRect = UIScreen.main.bounds
        dragViewDefaultTopMargin = containerView.frame.height - viewDefaultHeight
        
        let customFrame =  CGRect(x: 0, y:dragViewDefaultTopMargin , width: containerView.frame.width , height: containerView.frame.height - dragViewAnimatedTopMargin)
        
        if(viewHandler != nil){
            self.viewHandler(view, customFrame)
        }
        
//        super.init(frame: customFrame)
        
        view.frame = customFrame
        
        view.backgroundColor = viewBackgroundColor//.withAlphaComponent(0.20) //
        view.layer.cornerRadius = self.viewCornerRadius
        
        view.clipsToBounds = true
        
        
        self.view = view
        
//        let blur = UIBlurEffect(style: .light)
//        let blurView = UIVisualEffectView(effect: blur)
//        blurView.frame = self.bounds
//        blurView.clipsToBounds = true
//        blurView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
//        blurView.layer.cornerRadius = self.viewCornerRadius
//        self.addSubview(blurView)
//        
//        let button = UIButton(frame: CGRect(x: 20, y: 10, width: self.frame.width - 40, height: 15))
//        button.backgroundColor = UIColor.darkGray
//        button.autoresizingMask = [.flexibleHeight,.flexibleLeftMargin,.flexibleRightMargin]
//        button.layer.cornerRadius = 8
//        button.setTitle("", for: .normal)
//        button.addTarget(self, action: #selector(buttonAction), for: .touchUpInside)
//        self.addSubview(button)
        
        
        view.layoutIfNeeded()
        
        gestureRecognizer = UIPanGestureRecognizer(target: self, action: #selector(handlePan))
        view.addGestureRecognizer(gestureRecognizer)
    }
    
    @IBAction func handlePan(_ gestureRecognizer: UIPanGestureRecognizer) {
        
        if gestureRecognizer.state == .began || gestureRecognizer.state == .changed {
            
            if(handler != nil){
                handler(true,false,true,false,self.view.frame)
            }
            var newTranslation = CGPoint()
            var oldTranslation = CGPoint()
            newTranslation = gestureRecognizer.translation(in: view.superview)
            
            
            if(!(newTranslation.y < 0 && view.frame.origin.y + newTranslation.y <= dragViewAnimatedTopMargin))
            {
                view.translatesAutoresizingMaskIntoConstraints = true
                view.center = CGPoint(x: view.center.x, y: view.center.y + newTranslation.y)
                
                if (newTranslation.y < 0)
                {
                    if("\(view.frame.size.width)" != "\(String(describing: self.view.superview?.frame.size.width))")
                    {
                        
                        view.frame = CGRect(x: view.frame.origin.x - 2, y:view.frame.origin.y , width: view.frame.size.width + 4, height: view.frame.size.height)
                    }
                }
                else
                {
                    if("\(view.frame.size.width)" != "\((view.superview?.frame.size.width)! )")
                    {
                        view.frame = CGRect(x: view.frame.origin.x + 2, y: view.frame.origin.y , width: view.frame.size.width - 4, height: view.frame.size.height)
                    }
                }
                
                // self.layoutIfNeeded()
                gestureRecognizer.setTranslation(CGPoint.zero, in: view.superview)
                
                oldTranslation.y = newTranslation.y
            }
            else
            {
                view.frame.origin.y = dragViewAnimatedTopMargin
                
                view.isUserInteractionEnabled = false
            }
            
        }
        else if (gestureRecognizer.state == .ended)
        {
            
            view.isUserInteractionEnabled = true
            let vel = gestureRecognizer.velocity(in: view.superview)
            
            
            let finalY: CGFloat = 50.0
            let curY: CGFloat = self.view.frame.origin.y
            let distance: CGFloat = curY - finalY
            
            
            let springVelocity: CGFloat = 1.0 * vel.y / distance
            
            
            if(springVelocity > 0 && view.frame.origin.y <= dragViewAnimatedTopMargin)
            {
                if(handler != nil){
                    handler(true,false,false,true,self.view.frame)
                }
                
                view.frame = CGRect(x: 0, y: view.frame.origin.y , width: (view.superview?.frame.size.width)!, height: view.frame.size.height)
            }
            else if (springVelocity > 0)
            {
                
                if (view.frame.origin.y < (view.superview?.frame.size.height)!/3 && springVelocity < 7)
                {
                    UIView.animate(withDuration: 0.5, delay: 0.0, usingSpringWithDamping: 6, initialSpringVelocity: 1, options: UIViewAnimationOptions.curveEaseOut, animations: ({
                        if("\(self.view.frame.size.width)" != "\(String(describing: self.view.superview?.frame.size.width))")
                        {
                            
                            self.view.frame = CGRect(x: 0, y: self.view.frame.origin.y , width: (self.view.superview?.frame.size.width)!, height: self.view.frame.size.height)
                        }
                        if(self.handler != nil){
                            self.handler(true,false,false,true,self.view.frame)
                        }
                        self.view.frame.origin.y = self.dragViewAnimatedTopMargin
                    }), completion: nil)
                }
                else
                {
                    UIView.animate(withDuration: 0.5, delay: 0.0, usingSpringWithDamping: 0.5, initialSpringVelocity: 1, options: UIViewAnimationOptions.curveEaseOut, animations: ({
                        
                        if(self.view.frame.size.width != (self.view.superview?.frame.size.width)! )
                        {
                            
                            self.view.frame = CGRect(x: 0, y: self.view.frame.origin.y , width: (self.view.superview?.frame.size.width)! , height: self.view.frame.size.height)
                        }
                        if(self.handler != nil){
                            self.handler(false,true,false,true,self.view.frame)
                        }
                        self.view.frame.origin.y = self.dragViewDefaultTopMargin
                    }), completion:  { (finished: Bool) in
                        
                    })
                }
            }
            else if (springVelocity == 0)// If Velocity zero remain at same position
            {
                
                UIView.animate(withDuration: 0.5, delay: 0.0, usingSpringWithDamping: 0.5, initialSpringVelocity: 1, options: UIViewAnimationOptions.curveEaseOut, animations: ({
                    
                    self.view.frame.origin.y = CGFloat(self.viewLastYPosition)
                    
                    if(self.view.frame.origin.y == self.dragViewDefaultTopMargin)
                    {
                        if("\(self.view.frame.size.width)" == "\(String(describing: self.view.superview?.frame.size.width))")
                        {
                            
                            self.view.frame = CGRect(x: 0, y: self.view.frame.origin.y , width: self.view.frame.size.width , height: self.view.frame.size.height)
                            if(self.handler != nil){
                                self.handler(false,true,false,true,self.view.frame)
                            }
                        }
                    }
                    else{
                        if("\(self.view.frame.size.width)" != "\(String(describing: self.view.superview?.frame.size.width))")
                        {
                            
                            
                            self.view.frame = CGRect(x: 0, y: self.view.frame.origin.y , width: (self.view.superview?.frame.size.width)!, height: self.view.frame.size.height)
                            
                            if(self.handler != nil){
                                self.handler(true,false,false,true,self.view.frame)
                            }
                        }
                    }
                    
                }), completion: nil)
            }
            else
            {
                if(handler != nil){
                    handler(true,false,false,true,self.view.frame)
                }
                
                if("\(view.frame.size.width)" != "\(String(describing: view.superview?.frame.size.width))")
                {
                    view.frame = CGRect(x: 0, y: view.frame.origin.y , width: (view.superview?.frame.size.width)!, height: view.frame.size.height)
                }
                
                
                UIView.animate(withDuration: 0.5, delay: 0.0, usingSpringWithDamping: 6, initialSpringVelocity: 1, options: UIViewAnimationOptions.curveEaseOut, animations: ({
                    
                    
                    self.view.frame.origin.y = self.dragViewAnimatedTopMargin
                }), completion: nil)
            }
            viewLastYPosition = Double(view.frame.origin.y)
            
            view.addGestureRecognizer(gestureRecognizer)
        }
        
    }
    
    
    func buttonAction(sender: UIButton!) {
        
        if(view.frame.origin.y == dragViewAnimatedTopMargin)
        {
            if(handler != nil){
                handler(false,true,false,false,self.view.frame)
            }
            UIView.animate(withDuration: 0.5, delay: 0.0, usingSpringWithDamping: 0.5, initialSpringVelocity: 1, options: UIViewAnimationOptions.curveEaseOut, animations: ({
                
                self.view.frame = CGRect(x: 0, y: self.dragViewDefaultTopMargin , width: UIScreen.main.bounds.width , height: self.view.frame.size.height)
                
            }), completion: nil)
            
        }
        else{
            
            
            if(handler != nil){
                handler(true,false,false,false,self.view.frame)
            }
            
            UIView.animate(withDuration: 0.5, delay: 0.0, usingSpringWithDamping: 6, initialSpringVelocity: 1, options: UIViewAnimationOptions.curveEaseOut, animations: ({
                
                self.view.frame = CGRect(x:0, y:self.dragViewAnimatedTopMargin , width: UIScreen.main.bounds.width, height: self.view.frame.size.height)
                
            }), completion: nil)
        }
    }
    
}
