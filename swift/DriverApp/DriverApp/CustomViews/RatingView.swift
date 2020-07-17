//
//  RatingView.swift
//
//  Created by Peter Prokop on 18/10/15.
//  Copyright © 2015 Peter Prokop. All rights reserved.
//

import UIKit

@objc public protocol RatingViewDelegate {
    /**
     Called when user's touch ends
     
     - parameter ratingView: Rating view, which calls this method
     - parameter didChangeRating newRating: New rating
     */
    func ratingView(_ ratingView: RatingView, didChangeRating newRating: Float)
}

/**
 Rating bar, fully customisable from Interface builder
 */

open class RatingView: UIView {
    
    /// Total number of stars
    @IBInspectable open var starCount: Int = 5
    
    /// Image of unlit star, if nil "starryStars_off" is used
    @IBInspectable open var offImage: UIImage?
    
    /// Image of fully lit star, if nil "starryStars_on" is used
    @IBInspectable open var onImage: UIImage?
    
    /// Image of half-lit star, if nil "starryStars_half" is used
    @IBInspectable open var halfImage: UIImage?
    
    /// Current rating, updates star images after setting
    @IBInspectable open var rating: Float = Float(0) {
        didSet {
            // If rating is more than starCount simply set it to starCount
            rating = min(Float(starCount), rating)
            
            updateRating()
        }
    }
    
    /// If set to "false" only full stars will be lit
    @IBInspectable open var halfStarsAllowed: Bool = true
    
    /// If set to "false" user will not be able to edit the rating
    @IBInspectable open var editable: Bool = false
    
    /// If set to "false" only full stars will be lit
    @IBInspectable open var starFrameSize: CGFloat = 40
    
    
    @IBInspectable open var fullStarColor:UIColor!
    @IBInspectable open var emptyStarColor:UIColor!
    
    @IBInspectable open var isAppThemeColor: Bool = false
    
    
    /// Delegate, must confrom to *RatingViewDelegate* protocol
    open weak var delegate: RatingViewDelegate?
    
    @IBInspectable open var isAppThemeBackground: Bool = false
    
    var stars = [UIImageView]()
    
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        
        customInit()
    }
    
    required public init?(coder aDecoder: NSCoder) {
        super.init(coder: aDecoder)
    }
    
    override open func awakeFromNib() {
        super.awakeFromNib()
        
        customInit()
    }
    
    override open func prepareForInterfaceBuilder() {
        super.prepareForInterfaceBuilder()
        
        customInit()
    }
    
    func customInit() {
        
//        if(Configurations.isRTLMode()){
//            self.transform = CGAffineTransform(scaleX: -1, y: 1)
//        }

        
        let bundle = Bundle(for: RatingView.self)
        
        let size = CGSize(width: starFrameSize, height: starFrameSize)
        
        if offImage == nil {
            offImage = UIImage(named: "ic_empty_rating", in: bundle, compatibleWith: self.traitCollection)
            offImage = imageResize(offImage!,sizeChange: size)
        }
        if onImage == nil {
            onImage = UIImage(named: "ic_filled_rating", in: bundle, compatibleWith: self.traitCollection)
            onImage = imageResize(onImage!,sizeChange: size)
        }
        if halfImage == nil {
            halfImage = UIImage(named: "ic_half_rating", in: bundle, compatibleWith: self.traitCollection)
            halfImage = imageResize(halfImage!,sizeChange: size)
        }
        
        if(isAppThemeBackground == true){
            halfImage = halfImage?.tint(with: UIColor.UCAColor.AppThemeColor.darker(by: 20)!)
            onImage = onImage?.tint(with: UIColor.UCAColor.AppThemeTxtColor)
            offImage = offImage?.tint(with: emptyStarColor != nil ? emptyStarColor : UIColor.UCAColor.AppThemeColor.darker(by: 25)!)
            
            
            var halfImageOn = UIImage(named: "ic_half_rating_true", in: bundle, compatibleWith: self.traitCollection)
            halfImageOn = imageResize(halfImageOn!,sizeChange: size).tint(with: UIColor.UCAColor.AppThemeTxtColor)
            var halfImageOff = UIImage(named: "ic_half_rating_false", in: bundle, compatibleWith: self.traitCollection)
            halfImageOff = imageResize(halfImageOff!,sizeChange: size).tint(with: UIColor.UCAColor.AppThemeColor.darker(by: 20)!)
            
            let volleyballImage = CIImage(image: halfImageOn!)
            let otherImage = CIImage(image: halfImageOff!)
            let compositeFilter = CIFilter(name: "CIAdditionCompositing")!
            
            compositeFilter.setValue(volleyballImage,
                                     forKey: kCIInputImageKey)
            compositeFilter.setValue(otherImage,
                                     forKey: kCIInputBackgroundImageKey)
            
            if let compositeImage = compositeFilter.outputImage{
                let image = convert(cmage: compositeImage)
                halfImage = image
                
                // do something with the "merged" image
            }
        }
        
        if(isAppThemeColor){
            halfImage = halfImage?.tint(with: UIColor.UCAColor.AppThemeColor)
            onImage = onImage?.tint(with: UIColor.UCAColor.AppThemeColor)
            offImage = offImage?.tint(with: emptyStarColor != nil ? emptyStarColor : UIColor.UCAColor.AppThemeColor.darker(by: 25)!)
            
            
            var halfImageOn = UIImage(named: "ic_half_rating_true", in: bundle, compatibleWith: self.traitCollection)
            halfImageOn = imageResize(halfImageOn!,sizeChange: size).tint(with: UIColor.UCAColor.AppThemeColor)
            var halfImageOff = UIImage(named: "ic_half_rating_false", in: bundle, compatibleWith: self.traitCollection)
            halfImageOff = imageResize(halfImageOff!,sizeChange: size).tint(with: emptyStarColor != nil ? emptyStarColor : UIColor.UCAColor.AppThemeColor.darker(by: 25)!)
            
            let volleyballImage = CIImage(image: halfImageOn!)
            let otherImage = CIImage(image: halfImageOff!)
            let compositeFilter = CIFilter(name: "CIAdditionCompositing")!
            
            compositeFilter.setValue(volleyballImage,
                                     forKey: kCIInputImageKey)
            compositeFilter.setValue(otherImage,
                                     forKey: kCIInputBackgroundImageKey)
            
            if let compositeImage = compositeFilter.outputImage{
                let image = convert(cmage: compositeImage)
                halfImage = image
                
                // do something with the "merged" image
            }
        }
        
        guard let offImage = offImage else {
            assert(false, "offImage is not set")
            return
        }
        
        var i = 1
        while i <= starCount {
            let iv = UIImageView(image: offImage)
            
            addSubview(iv)
            stars.append(iv)
            i += 1
        }
        
        layoutStars()
        updateRating()
    }
    
    func convert(cmage:CIImage) -> UIImage
    {
        let context:CIContext = CIContext.init(options: nil)
        let cgImage:CGImage = context.createCGImage(cmage, from: cmage.extent)!
        let image:UIImage = UIImage.init(cgImage: cgImage)
        return image
    }
    
    func imageResize (_ image:UIImage, sizeChange:CGSize)-> UIImage{
        
        let hasAlpha = true
        let scale: CGFloat = 0.0 // Use scale factor of main screen
        
        UIGraphicsBeginImageContextWithOptions(sizeChange, !hasAlpha, scale)
        image.draw(in: CGRect(origin: CGPoint.zero, size: sizeChange))
        
        let scaledImage = UIGraphicsGetImageFromCurrentImageContext()
        return scaledImage!
    }
    
    override open func layoutSubviews() {
        super.layoutSubviews()
        
        layoutStars()
    }
    
    func layoutStars() {
        if stars.count != 0,
            let offImage = stars.first?.image {
            let halfWidth = offImage.size.width/2
            let distance = (bounds.size.width - (offImage.size.width * CGFloat(starCount))) / CGFloat(starCount + 1) + halfWidth
            
            var i = 1
            for iv in stars {
                iv.frame = CGRect(x: 0, y: 0, width: offImage.size.width, height: offImage.size.height)
                
                iv.center = CGPoint(x: CGFloat(i) * distance + halfWidth * CGFloat(i - 1),
                                        y: self.frame.size.height/2)
                i += 1
            }
        }
    }
    
    /**
     Compute and adjust rating when user touches begin/move/end
     */
    func handleTouches(_ touches: Set<UITouch>) {
        let touch = touches.first!
        let touchLocation = touch.location(in: self)
        
        var i = starCount - 1
        while i >= 0 {
            let imageView = stars[i]
            
            let x = touchLocation.x;
            
            if x >= imageView.center.x {
                rating = Float(i) + 1
                return
            } else if x >= imageView.frame.minX && halfStarsAllowed {
                rating = Float(i) + 0.5
                return
            }
            i -= 1
        }
        
        
        rating = 0
    }
//    func handleTouches(_ touches: Set<UITouch>) {
//        let touch = touches.first!
//        let touchLocation = touch.location(in: self)
//        
//        for var i = starCount - 1; i >= 0; i -= 1 {
//            let imageView = stars[i]
//            
//            let x = touchLocation.x;
//            
//            if x >= imageView.center.x {
//                rating = Float(i) + 1
//                return
//            } else if x >= imageView.frame.minX && halfStarsAllowed {
//                rating = Float(i) + 0.5
//                return
//            }
//        }
//        
//        rating = 0
//    }
    
    /**
     Adjust images on image views to represent new rating
     */
    
    func updateRating() {
        // To avoid crash when using IB
        if stars.count == 0 {
            return
        }
        
        // Set every full star
        var i = 1
        let fullRatngCount = editable ? Int(rating) : Int(Double(rating).rounded())
        while i <= fullRatngCount {
            let star = stars[i-1]
            star.image = onImage
            i += 1
        }
        
        if i > starCount {
            return
        }
        
        // Now add a half star
        if(editable){
            if rating - Float(i) + 1 >= 0.5 {
                let star = stars[i-1]
                star.image = halfImage
                i += 1
            }
        }else{
            if ((rating - Float(i) + 1 > 0) && (rating - Float(i) + 1 < 0.51)) {
                let star = stars[i-1]
                star.image = halfImage
                i += 1
            }
//            else if(rating - Float(i) + 1 >= 0.50){
//                let star = stars[i-1]
//                star.image = onImage
//                i += 1
//            }
        }
        
        while i <= starCount {
            let star = stars[i-1]
            star.image = offImage
            i += 1
        }
    }
    
//    func updateRating() {
//        // To avoid crash when using IB
//        if stars.count == 0 {
//            return
//        }
//
//        // Set every full star
//        var i = 1
//        while i <= Int(rating) {
//            let star = stars[i-1]
//            star.image = onImage
//            i += 1
//        }
//
//        if i > starCount {
//            return
//        }
//
//        // Now add a half star
//        if (rating - Float(i) + 1 >= 0.25) && (rating - Float(i) + 1 < 0.50) {
//            let star = stars[i-1]
//            star.image = halfImage
//            i += 1
//        }else if(rating - Float(i) + 1 >= 0.50){
//            let star = stars[i-1]
//            star.image = onImage
//            i += 1
//        }
//
//        while i <= starCount {
//            let star = stars[i-1]
//            star.image = offImage
//            i += 1
//        }
//    }
}
//    func updateRating() {
//        // To avoid crash when using IB
//        if stars.count == 0 {
//            return
//        }
//        
//        // Set every full star
//        var i = 1
//        for ; i <= Int(rating); i += 1 {
//            let star = stars[i-1]
//            star.image = onImage
//        }
//        
//        if i > starCount {
//            return
//        }
//        
//        // Now add a half star
//        if rating - Float(i) + 1 >= 0.5 {
//            let star = stars[i-1]
//            star.image = halfImage
//            i += 1
//        }
//        
//        
//        for ; i <= starCount; i += 1 {
//            let star = stars[i-1]
//            star.image = offImage
//        }
//    }


// MARK: Override UIResponder methods

extension RatingView {
    override open func touchesBegan(_ touches: Set<UITouch>, with event: UIEvent?) {
        guard editable else { return }
        handleTouches(touches)
    }
    
    override open func touchesMoved(_ touches: Set<UITouch>, with event: UIEvent?) {
        guard editable else { return }
        handleTouches(touches)
    }
    
    override open func touchesEnded(_ touches: Set<UITouch>, with event: UIEvent?) {
        guard editable else { return }
        handleTouches(touches)
        guard let delegate = delegate else { return }
        delegate.ratingView(self, didChangeRating: rating)
    }
}
