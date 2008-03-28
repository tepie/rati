/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.global;

import java.io.File;
import java.io.IOException;

/**
 *
 * @author terry
 */
public class FileNameMaker {
    
    public static String TEMP_PREFIX = "RATI_";
    public static String TEMP_SUFFIX = ".RATI";
    
    public static File makeAName() throws IOException{
        long time = System.nanoTime();
        File tempFile = File.createTempFile(TEMP_PREFIX + time, TEMP_SUFFIX);
        return tempFile;
    }

}
