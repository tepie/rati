/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import java.io.DataInputStream;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import org.rati.global.FileNameMaker;
import org.rati.global.RatiLogger;

/**
 *
 * @author terry
 */
public class PerformGraphImageDraw {
    
    public static String CONTENT_TYPE_PNG = "image/png;charset=UTF-8";
    public static String COMMAND_PREFIX = "circo -Tpng ";

    public PerformGraphImageDraw() {
        
    }

    public synchronized DataInputStream perform(String graphvizText){
        try {
            String filePath = this.writeToDisk(graphvizText);
            if(filePath != null){
                String command = COMMAND_PREFIX + filePath;
                Runtime runtime = Runtime.getRuntime();
                Process process = runtime.exec(command);
                return new DataInputStream(process.getInputStream());
            } else{
                RatiLogger.getLogger(PerformGraphImageDraw.class).warn("File path is null, cannot peform image draw");
                return null;
            }
        } catch (IOException ex) {
            ex.printStackTrace();
            RatiLogger.getLogger(PerformGraphImageDraw.class).error(ex.getMessage());
           return null;
        }
    }

    private synchronized String writeToDisk(String graphvizText) throws IOException {
        if(graphvizText != null){
            File tempFile = FileNameMaker.makeAName();
            FileWriter tempWriter = new FileWriter(tempFile);
            tempWriter.write(graphvizText);
            tempWriter.close();
            return tempFile.getAbsolutePath();
        } else {
            RatiLogger.getLogger(PerformGraphImageDraw.class).warn("Graphviz text is null, cannot peform image draw");
            return null;
        }
    }
}
